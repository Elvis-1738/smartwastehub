<?php
require __DIR__ . '/../../vendor/autoload.php';

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class WasteVisionAI {

    public static function analyzeImage($imagePath) 
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/vision-key.json');

        $client = new ImageAnnotatorClient();

        try {
            $image = file_get_contents($imagePath);
            $response = $client->labelDetection($image);
            $labels = $response->getLabelAnnotations();

            if (!$labels) return null;

            $results = [];
            foreach ($labels as $label) {
                $results[] = strtolower($label->getDescription());
            }

            return $results;

        } catch (Exception $e) {
            return null;
        }
    }

    // Convert labels → waste category
    public static function categorizeWaste($labels)
    {
        if (!$labels) return "unknown";

        $mapping = [
            "plastic"    => ["plastic", "bottle", "container", "polyethylene"],
            "organic"    => ["food", "banana", "fruit", "vegetable", "leaf"],
            "paper"      => ["paper", "cardboard", "newspaper"],
            "metal"      => ["metal", "tin", "can", "scrap"],
            "glass"      => ["glass", "jar", "drinking glass"],
            "ewaste"     => ["electronics", "circuit", "phone", "computer"],
        ];

        foreach ($mapping as $category => $keywords) {
            foreach ($keywords as $word) {
                if (in_array($word, $labels)) {
                    return $category;
                }
            }
        }

        return "unknown";
    }
}
?>
