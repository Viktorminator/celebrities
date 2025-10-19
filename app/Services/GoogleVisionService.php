<?php

namespace App\Services;

use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
use Illuminate\Support\Facades\Log;

class GoogleVisionService
{
    protected $client;

    public function __construct()
    {
        try {
            // Initialize Google Cloud Vision client
            $credentialsPath = config('services.google.vision.credentials_path');

            if (!$credentialsPath || !file_exists($credentialsPath)) {
                throw new \Exception('Google Cloud Vision credentials file not found at: ' . $credentialsPath);
            }

            $this->client = new ImageAnnotatorClient([
                'credentials' => $credentialsPath
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Vision client: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Detect clothing items and celebrities in an image
     *
     * @param string $imagePath Full path to the image file
     * @return array
     */
    public function detectClothing($imagePath)
    {
        try {
            if (!file_exists($imagePath)) {
                throw new \Exception("Image file not found: {$imagePath}");
            }

            $imageContent = file_get_contents($imagePath);

            if ($imageContent === false) {
                throw new \Exception("Failed to read image file: {$imagePath}");
            }

            // Create Image object
            $image = new Image();
            $image->setContent($imageContent);

            // Create features for the request - including celebrity detection
            $features = [
                // Label Detection
                (new Feature())->setType(Type::LABEL_DETECTION)->setMaxResults(20),
                // Object Localization (for bounding boxes)
                (new Feature())->setType(Type::OBJECT_LOCALIZATION)->setMaxResults(20),
                // Image Properties (for colors)
                (new Feature())->setType(Type::IMAGE_PROPERTIES)->setMaxResults(10),
                // Web Detection (for celebrity identification)
                (new Feature())->setType(Type::WEB_DETECTION)->setMaxResults(10),
                // Face Detection (detect faces and emotions)
                (new Feature())->setType(Type::FACE_DETECTION)->setMaxResults(10),
            ];

            // Create annotate image request
            $request = new AnnotateImageRequest();
            $request->setImage($image);
            $request->setFeatures($features);

            // Create batch request
            $batchRequest = new BatchAnnotateImagesRequest();
            $batchRequest->setRequests([$request]);

            // Perform the request
            $response = $this->client->batchAnnotateImages($batchRequest);
            $annotations = $response->getResponses()[0];

            // Check for errors
            if ($annotations->hasError()) {
                $error = $annotations->getError();
                throw new \Exception('Vision API error: ' . $error->getMessage());
            }

            // Get annotations
            $labels = $annotations->getLabelAnnotations();
            $objects = $annotations->getLocalizedObjectAnnotations();
            $imageProps = $annotations->getImagePropertiesAnnotation();
            $colors = $imageProps ? $imageProps->getDominantColors()->getColors() : [];

            // === CLOTHING DETECTION ===
            $detectedItems = [];

            // Define clothing-related categories
            $clothingCategories = [
                'Clothing', 'Shirt', 'Pants', 'Dress', 'Skirt', 'Jacket',
                'Coat', 'Sweater', 'Jeans', 'Shorts', 'T-shirt', 'Blouse',
                'Suit', 'Footwear', 'Shoe', 'Boot', 'Sneaker', 'Sandal',
                'Accessory', 'Hat', 'Bag', 'Sunglasses', 'Watch', 'Belt',
                'Scarf', 'Tie', 'Glove', 'Sock', 'Underwear', 'Swimwear',
                'Top', 'Bottom', 'Outerwear', 'Active wear', 'Fashion accessory',
                'Sleeve', 'Collar', 'Pocket', 'Zipper', 'Button'
            ];

            // Process object annotations (these have bounding boxes and are more accurate)
            if ($objects) {
                foreach ($objects as $object) {
                    $objectName = $object->getName();

                    // Check if it's a clothing item
                    if ($this->isClothingItem($objectName, $clothingCategories)) {
                        $vertices = $object->getBoundingPoly()->getNormalizedVertices();

                        $detectedItems[] = [
                            'category' => $this->normalizeCategory($objectName),
                            'description' => $objectName,
                            'confidence' => round($object->getScore() * 100, 2),
                            'bounding_box' => $this->extractBoundingBox($vertices),
                            'color' => $this->getDominantColor($colors),
                            'raw_data' => [
                                'name' => $object->getName(),
                                'score' => $object->getScore(),
                                'mid' => $object->getMid() ?? null
                            ]
                        ];
                    }
                }
            }

            // If no objects detected, fall back to labels
            if (empty($detectedItems) && $labels) {
                foreach ($labels as $label) {
                    $labelName = $label->getDescription();

                    if ($this->isClothingItem($labelName, $clothingCategories)) {
                        $detectedItems[] = [
                            'category' => $this->normalizeCategory($labelName),
                            'description' => $labelName,
                            'confidence' => round($label->getScore() * 100, 2),
                            'bounding_box' => null,
                            'color' => $this->getDominantColor($colors),
                            'raw_data' => [
                                'description' => $label->getDescription(),
                                'score' => $label->getScore(),
                                'mid' => $label->getMid() ?? null,
                                'topicality' => $label->getTopicality() ?? null
                            ]
                        ];
                    }
                }
            }

            // Remove duplicates based on similar descriptions
            $detectedItems = $this->removeDuplicates($detectedItems);

            // === CELEBRITY DETECTION ===
            $detectedCelebrities = [];
            $faceCount = 0;
            $contextLabels = [];

            // Process Web Detection for celebrity identification
            if ($annotations->hasWebDetection()) {
                $webDetection = $annotations->getWebDetection();

                // Get web entities (these can include celebrity names)
                $webEntities = $webDetection->getWebEntities();

                foreach ($webEntities as $entity) {
                    if ($entity->getScore() > 0.5) { // Only high confidence
                        $description = $entity->getDescription();

                        // Filter for likely celebrity names
                        if ($this->isPotentialCelebrity($description)) {
                            $detectedCelebrities[] = [
                                'name' => $description,
                                'confidence' => round($entity->getScore() * 100, 2),
                                'entity_id' => $entity->getEntityId() ?? null,
                                'source' => 'web_entity'
                            ];
                        }
                    }
                }

                // Get best guess labels
                $bestGuessLabels = $webDetection->getBestGuessLabels();
                foreach ($bestGuessLabels as $label) {
                    $labelText = $label->getLabel();
                    if ($this->isPotentialCelebrity($labelText)) {
                        $detectedCelebrities[] = [
                            'name' => $labelText,
                            'confidence' => 85.0,
                            'entity_id' => null,
                            'source' => 'best_guess'
                        ];
                    }
                }
            }

            // Process Face Detection
            if ($annotations->getFaceAnnotations()) {
                $faces = $annotations->getFaceAnnotations();
                $faceCount = count($faces);
            }

            // Process Labels for context
            if ($labels) {
                foreach ($labels as $label) {
                    $contextLabels[] = [
                        'description' => $label->getDescription(),
                        'confidence' => round($label->getScore() * 100, 2)
                    ];
                }
            }

            // Remove duplicate celebrities and sort by confidence
            $detectedCelebrities = $this->removeDuplicateCelebrities($detectedCelebrities);
            usort($detectedCelebrities, function($a, $b) {
                return $b['confidence'] <=> $a['confidence'];
            });

            // Close the client
            $this->client->close();

            return [
                'success' => true,
                'items' => $detectedItems,
                'total_items' => count($detectedItems),
                'celebrities' => $detectedCelebrities,
                'face_count' => $faceCount,
                'context_labels' => array_slice($contextLabels, 0, 10),
                'has_person' => $this->hasPersonInImage($contextLabels),
            ];

        } catch (\Exception $e) {
            Log::error('Google Vision API error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($this->client)) {
                $this->client->close();
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'items' => [],
                'celebrities' => [],
                'face_count' => 0,
                'context_labels' => [],
                'has_person' => false,
            ];
        }
    }

    /**
     * Check if item is a clothing-related item
     *
     * @param string $itemName
     * @param array $categories
     * @return bool
     */
    private function isClothingItem($itemName, $categories)
    {
        foreach ($categories as $category) {
            if (stripos($itemName, $category) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Normalize category name to standard categories
     *
     * @param string $name
     * @return string
     */
    private function normalizeCategory($name)
    {
        $categoryMap = [
            // Tops
            'T-shirt' => 'shirt',
            'Blouse' => 'shirt',
            'Sweater' => 'top',
            'Top' => 'top',
            'Shirt' => 'shirt',

            // Outerwear
            'Jacket' => 'outerwear',
            'Coat' => 'outerwear',
            'Outerwear' => 'outerwear',

            // Bottoms
            'Jeans' => 'pants',
            'Shorts' => 'pants',
            'Pants' => 'pants',
            'Skirt' => 'bottom',
            'Bottom' => 'bottom',

            // Footwear
            'Shoe' => 'footwear',
            'Boot' => 'footwear',
            'Sneaker' => 'footwear',
            'Sandal' => 'footwear',
            'Footwear' => 'footwear',

            // Dresses
            'Dress' => 'dress',

            // Accessories
            'Accessory' => 'accessory',
            'Fashion accessory' => 'accessory',
            'Hat' => 'accessory',
            'Bag' => 'accessory',
            'Sunglasses' => 'accessory',
            'Watch' => 'accessory',
            'Belt' => 'accessory',
            'Scarf' => 'accessory',
            'Tie' => 'accessory',
        ];

        // Check exact match first
        if (isset($categoryMap[$name])) {
            return $categoryMap[$name];
        }

        // Check partial match
        foreach ($categoryMap as $key => $value) {
            if (stripos($name, $key) !== false) {
                return $value;
            }
        }

        // Default to lowercase name
        return strtolower($name);
    }

    /**
     * Extract bounding box coordinates from vertices
     *
     * @param mixed $vertices
     * @return array|null
     */
    private function extractBoundingBox($vertices)
    {
        if (empty($vertices)) {
            return null;
        }

        $coords = [];
        foreach ($vertices as $vertex) {
            $coords[] = [
                'x' => $vertex->getX(),
                'y' => $vertex->getY()
            ];
        }

        return $coords;
    }

    /**
     * Get dominant color from image colors
     *
     * @param mixed $colors
     * @return string|null
     */
    private function getDominantColor($colors)
    {
        if (empty($colors) || count($colors) === 0) {
            return null;
        }

        // Get the first (most dominant) color
        $dominantColor = $colors[0];
        $rgb = $dominantColor->getColor();

        $r = $rgb->getRed();
        $g = $rgb->getGreen();
        $b = $rgb->getBlue();

        return $this->rgbToColorName($r, $g, $b);
    }

    /**
     * Convert RGB values to color name
     *
     * @param int $r Red (0-255)
     * @param int $g Green (0-255)
     * @param int $b Blue (0-255)
     * @return string
     */
    private function rgbToColorName($r, $g, $b)
    {
        // White/Light colors
        if ($r > 200 && $g > 200 && $b > 200) {
            return 'white';
        }

        // Black/Dark colors
        if ($r < 50 && $g < 50 && $b < 50) {
            return 'black';
        }

        // Gray
        if (abs($r - $g) < 30 && abs($g - $b) < 30 && abs($r - $b) < 30) {
            if ($r > 130) return 'light gray';
            if ($r > 70) return 'gray';
            return 'dark gray';
        }

        // Primary and secondary colors
        $maxComponent = max($r, $g, $b);
        $minComponent = min($r, $g, $b);

        // Red family
        if ($r === $maxComponent) {
            if ($g > 150 && $b < 100) return 'orange';
            if ($b > 150) return 'pink';
            if ($r > 150 && $g < 100 && $b < 100) return 'red';
            if ($r > 100) return 'burgundy';
        }

        // Green family
        if ($g === $maxComponent) {
            if ($r > 150) return 'yellow';
            if ($b > 150) return 'cyan';
            if ($g > 150 && $r < 100 && $b < 100) return 'green';
            return 'olive';
        }

        // Blue family
        if ($b === $maxComponent) {
            if ($r > 150) return 'purple';
            if ($g > 150) return 'teal';
            if ($b > 150 && $r < 100 && $g < 100) return 'blue';
            return 'navy';
        }

        // Brown
        if ($r > 100 && $g > 50 && $g < 120 && $b < 80) {
            return 'brown';
        }

        return 'multicolor';
    }

    /**
     * Remove duplicate items based on similarity
     *
     * @param array $items
     * @return array
     */
    private function removeDuplicates($items)
    {
        if (empty($items)) {
            return $items;
        }

        $unique = [];
        $seen = [];

        foreach ($items as $item) {
            $key = strtolower($item['category'] . '_' . $item['color']);

            if (!isset($seen[$key])) {
                $unique[] = $item;
                $seen[$key] = true;
            } else {
                // Keep the one with higher confidence
                foreach ($unique as $index => $existingItem) {
                    $existingKey = strtolower($existingItem['category'] . '_' . $existingItem['color']);
                    if ($existingKey === $key && $item['confidence'] > $existingItem['confidence']) {
                        $unique[$index] = $item;
                        break;
                    }
                }
            }
        }

        return $unique;
    }

    /**
     * Check if a description is likely a celebrity name
     *
     * @param string $description
     * @return bool
     */
    private function isPotentialCelebrity($description)
    {
        // Basic heuristic: celebrity names are typically proper nouns with multiple words
        // and don't contain common non-name words
        if (empty($description)) {
            return false;
        }

        // Skip single-character or very short descriptions
        if (strlen($description) < 3) {
            return false;
        }

        // Common non-celebrity terms to exclude
        $nonCelebrityTerms = [
            'clothing', 'shirt', 'dress', 'pants', 'fashion', 'style', 'color',
            'background', 'image', 'photo', 'person', 'people', 'object',
            'place', 'location', 'event', 'generic', 'unknown'
        ];

        // Check if description contains non-celebrity terms
        foreach ($nonCelebrityTerms as $term) {
            if (stripos($description, $term) !== false) {
                return false;
            }
        }

        // Check if the description looks like a name (contains at least one space, suggesting a first/last name)
        if (preg_match('/\b[A-Z][a-z]*\s+[A-Z][a-z]*\b/', $description)) {
            return true;
        }

        // Additional check: description starts with a capital letter and has reasonable length
        if (ctype_upper(substr($description, 0, 1)) && strlen($description) > 5) {
            return true;
        }

        return false;
    }

    /**
     * Remove duplicate celebrities based on name
     *
     * @param array $celebrities
     * @return array
     */
    private function removeDuplicateCelebrities($celebrities)
    {
        if (empty($celebrities)) {
            return $celebrities;
        }

        $unique = [];
        $seen = [];

        foreach ($celebrities as $celebrity) {
            $key = strtolower($celebrity['name']);

            if (!isset($seen[$key])) {
                $unique[] = $celebrity;
                $seen[$key] = true;
            } else {
                // Keep the celebrity with the higher confidence score
                foreach ($unique as $index => $existingCelebrity) {
                    $existingKey = strtolower($existingCelebrity['name']);
                    if ($existingKey === $key && $celebrity['confidence'] > $existingCelebrity['confidence']) {
                        $unique[$index] = $celebrity;
                        break;
                    }
                }
            }
        }

        return $unique;
    }
    /**
     * Check if the image likely contains a person
     *
     * @param array $contextLabels
     * @return bool
     */
    private function hasPersonInImage($contextLabels)
    {
        // Terms that indicate the presence of a person
        $personIndicators = [
            'person', 'people', 'human', 'man', 'woman', 'child', 'face',
            'portrait', 'crowd', 'group', 'individual'
        ];

        // Check context labels for person-related terms
        foreach ($contextLabels as $label) {
            $description = strtolower($label['description']);
            foreach ($personIndicators as $indicator) {
                if (stripos($description, $indicator) !== false) {
                    return true;
                }
            }
        }

        // Optionally, consider face detection results (if available)
        // Since face_count is already calculated in detectClothing()
        // We could use it, but it's not passed here, so relying on labels is sufficient

        return false;
    }
    /**
     * Clean up and close the client connection
     */
    public function __destruct()
    {
        if (isset($this->client)) {
            try {
                $this->client->close();
            } catch (\Exception $e) {
                // Silently handle cleanup errors
                Log::debug('Google Vision client cleanup: ' . $e->getMessage());
            }
        }
    }
}
