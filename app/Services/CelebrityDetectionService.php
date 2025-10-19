<?php

namespace App\Services;

use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
use Illuminate\Support\Facades\Log;

class CelebrityDetectionService
{
    protected $client;

    public function __construct()
    {
        try {
            $credentialsPath = config('services.google.vision.credentials_path');

            if (!$credentialsPath || !file_exists($credentialsPath)) {
                throw new \Exception('Google Cloud Vision credentials file not found');
            }

            $this->client = new ImageAnnotatorClient([
                'credentials' => $credentialsPath
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to initialize Celebrity Detection client: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Detect celebrities and faces in an image
     *
     * @param string $imagePath Full path to the image file
     * @return array
     */
    public function detectCelebrity($imagePath)
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

            // Create features for the request
            $features = [
                // Web Detection - can identify celebrities through web entities
                (new Feature())->setType(Type::WEB_DETECTION)->setMaxResults(10),
                // Face Detection - detect faces and emotions
                (new Feature())->setType(Type::FACE_DETECTION)->setMaxResults(10),
                // Label Detection - general context
                (new Feature())->setType(Type::LABEL_DETECTION)->setMaxResults(20),
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

                        // Filter for likely celebrity names (entities with proper capitalization)
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
                            'confidence' => 85.0, // Best guess is usually high confidence
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

                // Get face characteristics
                foreach ($faces as $face) {
                    $faceInfo = [
                        'joy_likelihood' => $this->getLikelihoodString($face->getJoyLikelihood()),
                        'sorrow_likelihood' => $this->getLikelihoodString($face->getSorrowLikelihood()),
                        'anger_likelihood' => $this->getLikelihoodString($face->getAngerLikelihood()),
                        'surprise_likelihood' => $this->getLikelihoodString($face->getSurpriseLikelihood()),
                        'confidence' => round($face->getDetectionConfidence() * 100, 2),
                    ];
                }
            }

            // Process Labels for context
            if ($annotations->getLabelAnnotations()) {
                $labels = $annotations->getLabelAnnotations();
                foreach ($labels as $label) {
                    $contextLabels[] = [
                        'description' => $label->getDescription(),
                        'confidence' => round($label->getScore() * 100, 2)
                    ];
                }
            }

            // Close the client
            $this->client->close();

            // Remove duplicates and sort by confidence
            $detectedCelebrities = $this->removeDuplicateCelebrities($detectedCelebrities);
            usort($detectedCelebrities, function($a, $b) {
                return $b['confidence'] <=> $a['confidence'];
            });

            return [
                'success' => true,
                'celebrities' => $detectedCelebrities,
                'face_count' => $faceCount,
                'context_labels' => array_slice($contextLabels, 0, 10),
                'has_person' => $this->hasPersonInImage($contextLabels),
            ];

        } catch (\Exception $e) {
            Log::error('Celebrity Detection error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($this->client)) {
                $this->client->close();
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'celebrities' => [],
                'face_count' => 0,
                'context_labels' => [],
                'has_person' => false,
            ];
        }
    }

    /**
     * Check if description is likely a celebrity name
     */
    private function isPotentialCelebrity($description)
    {
        if (empty($description)) {
            return false;
        }

        // Check if it has proper capitalization (like a name)
        $words = explode(' ', $description);
        $capitalizedWords = 0;

        foreach ($words as $word) {
            if (!empty($word) && ctype_upper($word[0])) {
                $capitalizedWords++;
            }
        }

        // Must have at least one capitalized word
        if ($capitalizedWords === 0) {
            return false;
        }

        // Filter out common non-celebrity terms
        $excludeTerms = [
            'Person', 'People', 'Fashion', 'Style', 'Model', 'Photography',
            'Portrait', 'Photo', 'Image', 'Picture', 'Clothing', 'Dress',
            'Event', 'Red Carpet', 'Award', 'Show', 'Magazine', 'Cover',
            'Beauty', 'Makeup', 'Hair', 'Outfit', 'Look', 'Wear'
        ];

        foreach ($excludeTerms as $term) {
            if (stripos($description, $term) !== false && count($words) <= 2) {
                return false;
            }
        }

        // Check if it looks like a person's name (has at least 2 words or is a single capitalized word > 3 chars)
        if (count($words) >= 2 || (count($words) === 1 && strlen($description) > 3)) {
            return true;
        }

        return false;
    }

    /**
     * Remove duplicate celebrity entries
     */
    private function removeDuplicateCelebrities($celebrities)
    {
        $unique = [];
        $seen = [];

        foreach ($celebrities as $celebrity) {
            $key = strtolower(trim($celebrity['name']));

            if (!isset($seen[$key])) {
                $unique[] = $celebrity;
                $seen[$key] = true;
            } else {
                // Keep the one with higher confidence
                foreach ($unique as $index => $existingCelebrity) {
                    $existingKey = strtolower(trim($existingCelebrity['name']));
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
     * Convert likelihood enum to string
     */
    private function getLikelihoodString($likelihood)
    {
        $map = [
            0 => 'UNKNOWN',
            1 => 'VERY_UNLIKELY',
            2 => 'UNLIKELY',
            3 => 'POSSIBLE',
            4 => 'LIKELY',
            5 => 'VERY_LIKELY',
        ];

        return $map[$likelihood] ?? 'UNKNOWN';
    }

    /**
     * Check if image contains a person
     */
    private function hasPersonInImage($labels)
    {
        $personTerms = ['Person', 'People', 'Human', 'Face', 'Portrait', 'Man', 'Woman'];

        foreach ($labels as $label) {
            foreach ($personTerms as $term) {
                if (stripos($label['description'], $term) !== false && $label['confidence'] > 70) {
                    return true;
                }
            }
        }

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
                Log::debug('Celebrity Detection client cleanup: ' . $e->getMessage());
            }
        }
    }
}
