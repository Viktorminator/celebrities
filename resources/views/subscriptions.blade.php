@extends('layout')

@section('title', 'Subscriptions - Glamdar')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-display font-bold text-indigo-900 mb-4">Choose Your Glamdar Plan</h1>
            <p class="text-lg text-gray-600">Unlock more uploads, advanced analytics, and premium features</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white rounded-2xl shadow-lg border {{ $plan['is_popular'] ? 'border-indigo-500 relative' : 'border-transparent' }}">
                    @if($plan['is_popular'])
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-indigo-500 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">Most Popular</span>
                    @endif
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan['name'] }}</h2>
                        <p class="text-3xl font-extrabold text-indigo-600 mb-4">{{ $plan['price'] }}</p>
                        <p class="text-sm text-gray-500 mb-6">{{ $plan['description'] }}</p>

                        <div class="mb-6">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Usage Limit</p>
                            <p class="text-base font-medium text-gray-900">{{ $plan['limit'] }}</p>
                        </div>

                        <ul class="space-y-3 mb-8">
                            @foreach($plan['features'] as $feature)
                                <li class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <button class="w-full px-6 py-3 rounded-full text-sm font-semibold transition-colors {{ $plan['is_popular'] ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                            @if($plan['name'] === 'Free')
                                Current Plan
                            @else
                                Upgrade to {{ $plan['name'] }}
                            @endif
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 bg-white rounded-2xl shadow-md p-8">
            <h3 class="text-xl font-bold text-indigo-900 mb-4">Plan Comparison</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feature</th>
                            @foreach($plans as $plan)
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $plan['name'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        <tr>
                            <td class="px-6 py-4 font-medium">Style Uploads</td>
                            <td class="px-6 py-4 text-center">10</td>
                            <td class="px-6 py-4 text-center">100</td>
                            <td class="px-6 py-4 text-center">Unlimited</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 font-medium">Advanced Analytics</td>
                            <td class="px-6 py-4 text-center text-gray-400"><i class="fas fa-minus"></i></td>
                            <td class="px-6 py-4 text-center text-green-500"><i class="fas fa-check"></i></td>
                            <td class="px-6 py-4 text-center text-green-500"><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium">Priority Support</td>
                            <td class="px-6 py-4 text-center text-gray-400"><i class="fas fa-minus"></i></td>
                            <td class="px-6 py-4 text-center text-green-500"><i class="fas fa-check"></i></td>
                            <td class="px-6 py-4 text-center text-green-500"><i class="fas fa-check"></i></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 font-medium">Affiliate Reporting</td>
                            <td class="px-6 py-4 text-center text-gray-400"><i class="fas fa-minus"></i></td>
                            <td class="px-6 py-4 text-center text-gray-400"><i class="fas fa-minus"></i></td>
                            <td class="px-6 py-4 text-center text-green-500"><i class="fas fa-check"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

