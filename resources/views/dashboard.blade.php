<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-2 overflow-hidden rounded-xl shadow-lg bg-gradient-to-br from-white to-gray-50">
                    <div class="p-8 text-gray-900">
                        <div class="flex items-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-3.866-3.582-7-8-7m0 14c4.418 0 8-3.134 8-7m0 0c0 3.866 3.582 7 8 7m0-14c-4.418 0-8 3.134-8 7" />
                            </svg>
                            <h3 class="ml-3 text-xl font-semibold">Your Current Plan & Usage</h3>
                        </div>
                        @if($plan)
                            <p class="text-3xl font-bold">{{ $plan->name }} Plan</p>

                            <div class="mt-8">
                                <p class="flex justify-between text-sm font-medium">
                                    <span>Courier Check Limit (Today)</span>
                                    <span>{{ $dailyUsage }} / {{ $plan->daily_courier_limit }}</span>
                                </p>
                                <div class="w-full bg-gray-200 rounded-full h-3 mt-3">
                                    @php
                                        $percentage = ($plan->daily_courier_limit > 0)? ($dailyUsage / $plan->daily_courier_limit) * 100 : 0;
                                    @endphp
                                    <div class="h-3 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600" style="width: {{ $percentage }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Resets daily at midnight (UTC).</p>
                            </div>
                        @else
                            <p>You are not subscribed to any plan.</p>
                        @endif
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl shadow-lg bg-gradient-to-br from-white to-gray-50">
                    <div class="p-8 text-gray-900">
                        <div class="flex items-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 00-10 0v2m10 0a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6a2 2 0 012-2m10 0H7" />
                            </svg>
                            <h3 class="ml-3 text-xl font-semibold">SMS Credits</h3>
                        </div>
                        <p class="text-5xl font-bold">{{ $smsCredit->balance?? 0 }}</p>
                        <p class="text-gray-600">credits remaining</p>
                        <div class="mt-6">
                            <x-primary-button disabled class="bg-gradient-to-r from-indigo-500 to-purple-600 border-0">
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
