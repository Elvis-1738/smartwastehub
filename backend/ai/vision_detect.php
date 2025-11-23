<?php
// backend/ai/vision_detect.php

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

require_once __DIR__ . '/../config.php';              // DB + session
require_once __DIR__ . '/../../vendor/autoload.php'; // Composer autoload

header('Content-Type: application/json');

// Only logged-in households (you can relax this if you want)
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'household') {
    echo json_encode([
        'success' => false,
        'error'   => 'Unauthorized',
    ]);
    exit;
}

// Check file upload
if (
    !isset($_FILES['image']) ||
    $_FILES['image']['error'] !== UPLOAD_ERR_OK
) {
    echo json_encode([
        'success' => false,
        'error'   => 'No image uploaded or upload error.',
    ]);
    exit;
}

$tmpPath = $_FILES['image']['tmp_name'];
$imageData = file_get_contents($tmpPath);

if (!$imageData) {
    echo json_encode([
        'success' => false,
        'error'   => 'Could not read uploaded file.',
    ]);
    exit;
}

try {
    // Create Vision client using your service account key
    $imageAnnotator = new ImageAnnotatorClient([
        'credentials' => __DIR__ . '/vision-key.json',
    ]);

    // Ask Google Vision for LABEL_DETECTION
    $response = $imageAnnotator->labelDetection($imageData);
    $labels   = $response->getLabelAnnotations();

    if ($response->getError() && $response->getError()->getMessage()) {
        throw new Exception($response->getError()->getMessage());
    }

    if (!$labels || count($labels) === 0) {
        echo json_encode([
            'success' => true,
            'message' => 'No labels detected.',
            'labels'  => [],
        ]);
        $imageAnnotator->close();
        exit;
    }

    // Collect top labels
    $rawLabels = [];
    foreach ($labels as $label) {
        $rawLabels[] = [
            'description' => $label->getDescription(),
            'score'       => $label->getScore(),
        ];
    }

    // --- SIMPLE MAPPING TO YOUR WASTE CATEGORIES ---

    // 1. Fetch categories from DB
    $categories = [];
    $res = $conn->query("SELECT id, name FROM waste_categories");
    while ($row = $res->fetch_assoc()) {
        $categories[] = $row; // ['id' => .., 'name' => ..]
    }

    // 2. Helper to guess which of your categories fits best
    $categoryGuess = null;
    $bestScore     = 0.0;

    foreach ($rawLabels as $l) {
        $desc = strtolower($l['description']);
        $score = (float)$l['score'];

        // Ignore very low-confidence labels
        if ($score < 0.50) {
            continue;
        }

        foreach ($categories as $cat) {
            $cname = strtolower($cat['name']);

            // Simple heuristics: if label text contains key words
            $match = false;

            if (strpos($cname, 'plastic') !== false &&
                (str_contains($desc, 'plastic') || str_contains($desc, 'bottle') || str_contains($desc, 'bag'))) {
                $match = true;
            }

            if (strpos($cname, 'organic') !== false &&
                (str_contains($desc, 'food') || str_contains($desc, 'fruit') || str_contains($desc, 'vegetable') || str_contains($desc, 'organic'))) {
                $match = true;
            }

            if ((strpos($cname, 'paper') !== false || strpos($cname, 'cardboard') !== false) &&
                (str_contains($desc, 'paper') || str_contains($desc, 'cardboard') || str_contains($desc, 'newspaper'))) {
                $match = true;
            }

            if (strpos($cname, 'metal') !== false &&
                (str_contains($desc, 'metal') || str_contains($desc, 'tin') || str_contains($desc, 'aluminum') || str_contains($desc, 'steel'))) {
                $match = true;
            }

            if ((strpos($cname, 'e-waste') !== false || strpos($cname, 'e-waste') !== false || strpos($cname, 'waste') !== false) &&
                (str_contains($desc, 'electronic') || str_contains($desc, 'computer') || str_contains($desc, 'phone') || str_contains($desc, 'circuit'))) {
                $match = true;
            }

            if ($match && $score > $bestScore) {
                $bestScore = $score;
                $categoryGuess = [
                    'id'    => $cat['id'],
                    'name'  => $cat['name'],
                    'label' => $l['description'],
                    'score' => $score,
                ];
            }
        }
    }

    $imageAnnotator->close();

    echo json_encode([
        'success'        => true,
        'category_guess' => $categoryGuess,
        'labels'         => $rawLabels,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => 'Vision API error: ' . $e->getMessage(),
    ]);
}
