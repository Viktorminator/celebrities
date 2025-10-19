<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AmazonProductService
{
    protected $accessKey;
    protected $secretKey;
    protected $associateTag;
    protected $region;
    protected $marketplace;

    public function __construct()
    {
        $this->accessKey = config('services.amazon.access_key');
        $this->secretKey = config('services.amazon.secret_key');
        $this->associateTag = config('services.amazon.associate_tag');
        $this->region = config('services.amazon.region', 'us-east-1');
        $this->marketplace = config('services.amazon.marketplace', 'www.amazon.com');
    }

    /**
     * Search for products on Amazon
     *
     * @param string $description Item description
     * @param string $category Item category
     * @return array
     */
    public function searchProducts($description, $category)
    {
        try {
            // For now, we'll use Amazon's simple affiliate links
            // If you want to use PA-API, you'll need to install the SDK:
            // composer require thewirecutter/paapi5-php-sdk

            $searchQuery = urlencode("{$description} {$category}");
            $affiliateLink = "https://{$this->marketplace}/s?k={$searchQuery}&tag={$this->associateTag}";

            // Return a simple affiliate search link
            // In production, you'd use PA-API to get specific products
            return [
                [
                    'platform' => 'Amazon',
                    'title' => ucfirst($description) . ' - Search Results',
                    'url' => $affiliateLink,
                    'price' => 'N/A',
                    'image_url' => null,
                    'asin' => null,
                    'search_query' => "{$description} {$category}"
                ]
            ];

            // Uncomment below if you have PA-API SDK installed and configured
            /*
            return $this->searchWithPaApi($description, $category);
            */

        } catch (\Exception $e) {
            Log::error('Amazon product search error: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Search using PA-API (requires SDK)
     * Uncomment and use this when PA-API SDK is installed
     */
    /*
    private function searchWithPaApi($description, $category)
    {
        $client = new \Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi(
            new \GuzzleHttp\Client(),
            $this->getPaApiConfig()
        );

        $searchItemsRequest = new \Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest();
        $searchItemsRequest->setPartnerTag($this->associateTag);
        $searchItemsRequest->setPartnerType('Associates');
        $searchItemsRequest->setKeywords("{$description} {$category}");
        $searchItemsRequest->setSearchIndex('Fashion');
        $searchItemsRequest->setItemCount(3);
        $searchItemsRequest->setResources([
            'ItemInfo.Title',
            'Offers.Listings.Price',
            'Images.Primary.Medium'
        ]);

        try {
            $response = $client->searchItems($searchItemsRequest);

            if ($response->getSearchResult() && $response->getSearchResult()->getItems()) {
                $products = [];

                foreach ($response->getSearchResult()->getItems() as $item) {
                    $products[] = [
                        'platform' => 'Amazon',
                        'title' => $item->getItemInfo()->getTitle()->getDisplayValue() ?? 'N/A',
                        'url' => $item->getDetailPageURL() ?? '',
                        'price' => $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() ?? 'N/A',
                        'image_url' => $item->getImages()->getPrimary()->getMedium()->getURL() ?? null,
                        'asin' => $item->getASIN() ?? 'N/A',
                        'search_query' => "{$description} {$category}"
                    ];
                }

                return $products;
            }

            return [];

        } catch (\Exception $e) {
            Log::error('PA-API error: ' . $e->getMessage());
            return [];
        }
    }

    private function getPaApiConfig()
    {
        $config = new \Amazon\ProductAdvertisingAPI\v1\Configuration();
        $config->setAccessKey($this->accessKey);
        $config->setSecretKey($this->secretKey);
        $config->setHost("webservices.amazon.{$this->region}");
        $config->setRegion($this->region);

        return $config;
    }
    */

    /**
     * Generate direct product affiliate link
     */
    public function generateAffiliateLink($asin)
    {
        return "https://{$this->marketplace}/dp/{$asin}?tag={$this->associateTag}";
    }
}
