<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Your Current Plan & Usage</h3>
                        @if($plan)
                            <p class="text-2xl font-bold">{{ $plan->name }} Plan</p>
                            
                            <div class="mt-6">
                                <p class="flex justify-between">
                                    <span><strong>Courier Check Limit (Today)</strong></span>
                                    <span>{{ $dailyUsage }} / {{ $plan->daily_courier_limit }}</span>
                                </p>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                    @php
                                        $percentage = ($plan->daily_courier_limit > 0)? ($dailyUsage / $plan->daily_courier_limit) * 100 : 0;
                                    @endphp
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Resets daily at midnight (UTC).</p>
                            </div>
                        @else
                            <p>You are not subscribed to any plan.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">SMS Credits</h3>
                        <p class="text-4xl font-bold">{{ $smsCredit->balance?? 0 }}</p>
                        <p class="text-gray-600">credits remaining</p>
                        <div class="mt-4">
                            <x-primary-button disabled>
                                {{ __('Buy More Credits') }}
                            </x-primary-button>
                            <p class="text-xs text-gray-500 mt-2">Coming Soon!</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>