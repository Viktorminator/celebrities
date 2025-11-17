<?php

namespace App\Http\Controllers;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = [
            [
                'name' => 'Free',
                'price' => '$0',
                'description' => 'Perfect for getting started with Glamdar',
                'limit' => 'Add up to 10 styles',
                'features' => [
                    'Upload up to 10 looks',
                    'Add custom product links',
                    'Basic analytics',
                    'Community support',
                ],
                'is_popular' => false,
            ],
            [
                'name' => 'Pro',
                'price' => '$19 / month',
                'description' => 'Ideal for bloggers and fashion enthusiasts',
                'limit' => 'Add up to 100 styles',
                'features' => [
                    'Upload up to 100 looks',
                    'Priority product matching',
                    'Detailed analytics dashboard',
                    'Shareable style collections',
                    'Priority support',
                ],
                'is_popular' => true,
            ],
            [
                'name' => 'Premium',
                'price' => '$49 / month',
                'description' => 'For brands and professional creators',
                'limit' => 'Unlimited styles',
                'features' => [
                    'Unlimited uploads',
                    'Advanced affiliate reporting',
                    'Team collaboration tools',
                    'Dedicated success manager',
                    'Custom branding',
                ],
                'is_popular' => false,
            ],
        ];

        return view('subscriptions', compact('plans'));
    }
}
