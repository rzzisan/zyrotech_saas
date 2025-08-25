<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Send Quick SMS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4">
                            <div class="font-medium text-red-600">Whoops! Something went wrong.</div>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('sms.send.handle') }}">
                        @csrf
                        <div>
                            <x-input-label for="recipients" :value="__('Recipients (comma-separated)')" />
                            <textarea id="recipients" name="recipients" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required autofocus>{{ old('recipients') }}</textarea>
                            <x-input-error :messages="$errors->get('recipients')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="message" :value="__('Message')" />
                            <textarea id="message" name="message" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="5" required>{{ old('message') }}</textarea>
                            <x-input-error :messages="$errors->get('message')" class="mt-2" />
                        </div>

                        <div class="mt-2 text-sm text-gray-500">
                            <span id="char-count">0</span> characters | <span id="sms-count">0</span> SMS
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Send SMS') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messageEl = document.getElementById('message');
            const charCountEl = document.getElementById('char-count');
            const smsCountEl = document.getElementById('sms-count');

            function isUnicode(str) {
                for (var i = 0, n = str.length; i < n; i++) {
                    if (str.charCodeAt(i) > 255) { return true; }
                }
                return false;
            }

            function updateSmsCount() {
                const message = messageEl.value;
                const length = message.length;
                let sms_count = 0;
                
                charCountEl.textContent = length;

                if (length === 0) {
                    sms_count = 0;
                } else if (isUnicode(message)) {
                    // বাংলা বার্তার জন্য প্রতি ৭০ অক্ষরে ১টি ক্রেডিট
                    sms_count = Math.ceil(length / 70);
                } else {
                    // ইংরেজি বার্তার জন্য প্রতি ১৬০ অক্ষরে ১টি ক্রেডিট
                    sms_count = Math.ceil(length / 160);
                }
                smsCountEl.textContent = sms_count;
            }

            messageEl.addEventListener('input', updateSmsCount);
            updateSmsCount(); // Initial count on page load
        });
    </script>
    @endpush
</x-app-layout>