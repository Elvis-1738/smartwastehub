<?php
header('Content-Type: application/json');

try {
    // Expecting multipart form-data
    if (!isset($_FILES['image'])) {
        echo json_encode(["success" => false, "error" => "No image uploaded"]);
        exit;
    }

    $imageData = file_get_contents($_FILES['image']['tmp_name']);

    require_once __DIR__ . '/../../vendor/autoload.php';

    // Point to your JSON credential
    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/vision-key.json');

    $client = new Google\Cloud\Vision\V1\ImageAnnotatorClient();

    $response = $client->labelDetection($imageData);
    $labels = $response->getLabelAnnotations();

    if (!$labels) {
        echo json_encode(["success" => false, "error" => "No labels returned"]);
        exit;
    }

    // Top label
    $top = strtolower($labels[0]->getDescription());
    $score = $labels[0]->getScore();

    // Local waste category lookup
    $map = [
        "plastic" => "Plastic",
        "bottle" => "Plastic",
        "paper" => "Paper & Cardboard",
        "cardboard" => "Paper & Cardboard",
        "metal" => "Metal",
        "tin" => "Metal",
        "food" => "Organic Waste",
        "banana" => "Organic Waste",
        "organic" => "Organic Waste",
        "electronics" => "E-Waste",
        "computer" => "E-Waste",
        "phone" => "E-Waste"
    ];

    $detectedCategoryName = "Unknown";
    foreach ($map as $k => $v) {
        if (str_contains($top, $k)) {
            $detectedCategoryName = $v;
            break;
        }
    }

    // Find matching category in DB
    require __DIR__ . '/../../config.php';

    $stmt = $conn->prepare("SELECT id, name FROM waste_categories WHERE name LIKE ? LIMIT 1");
    $like = "%" . $detectedCategoryName . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $cat = $stmt->get_result()->fetch_assoc();

    $categoryGuess = null;
    if ($cat) {
        $categoryGuess = [
            "id"    => $cat['id'],
            "name"  => $cat['name'],
            "label" => $top,
            "score" => $score
        ];
    }

    echo json_encode([
        "success"       => true,
        "labels"        => [$top],
        "category_guess" => $categoryGuess
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
