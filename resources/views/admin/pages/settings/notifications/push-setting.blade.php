<div>
    <div class="white-box">
        @php
            $clientConfig = getJsonFile('firebase/cloud-messaging.json', 'public');
            $adminConfig = getJsonFile('firebase/admin-sdk.json', 'public');
            $hasClientConfig = !empty($clientConfig);
            $hasAdminConfig = !empty($adminConfig);

            // Build the JSON display for client config (without vapidKey)
            $clientDisplayConfig = $clientConfig ? collect($clientConfig)->except('vapidKey')->toArray() : null;
        @endphp
        <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
            <p class="m-text font-medium">{{ __('Push Notification Settings') }}</p>
            <div class="flex gap-2 flex-wrap">
                <button type="button" data-modal-target="help" class="btn-primary outlined">
                    <i class="ph ph-question"></i>{{ __('Help') }}
                </button>
                <button type="button" data-modal-target="test-firebase-notification" class="btn-primary outlined">
                    <i class="ph ph-paper-plane-tilt"></i>{{ __('Test Notification') }}
                </button>
            </div>
            <x-admin::modal title="Get Help for Firebase Config" modalId="help">
                <div class="space-y-4">
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-primary/5 border border-primary/20">
                        <i class="ph ph-lightbulb text-primary text-xl mt-0.5"></i>
                        <div>
                            <p class="font-medium text-primary mb-1">{{ __('Quick Setup Guide') }}</p>
                            <p class="text-sm text-neutral-400">
                                {{ __('Copy and paste your Firebase configuration directly from the Firebase Console.') }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h6 class="font-medium mb-2 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-xs">1</span>
                                {{ __('Get Web App Config') }}
                            </h6>
                            <p class="text-sm text-neutral-400 pl-8">
                                {{ __('Go to Firebase Console → Project Settings → Your Apps → Web App. Copy the') }}
                                <code class="bg-neutral-0  px-1 rounded">firebaseConfig</code>
                                {{ __('object.') }}
                            </p>
                        </div>

                        <div>
                            <h6 class="font-medium mb-2 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-xs">2</span>
                                {{ __('Get VAPID Key') }}
                            </h6>
                            <p class="text-sm text-neutral-400 pl-8">
                                {{ __('Go to Project Settings → Cloud Messaging → Web Push certificates. Generate or copy your key pair.') }}
                            </p>
                        </div>

                        <div>
                            <h6 class="font-medium mb-2 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-xs">3</span>
                                {{ __('Download Service Account Key') }}
                            </h6>
                            <p class="text-sm text-neutral-400 pl-8">
                                {{ __('Go to Project Settings → Service accounts → Generate new private key. Download and upload the JSON file.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </x-admin::modal>
        </div>

        <div class="flex items-center gap-3 rounded-md border border-warning/40 px-4 py-3 bg-warning/5 mb-6">
            <i class="ph ph-warning text-warning text-xl"></i>
            <p class="text-warning s-text">
                {{ __('Your system must have a valid SSL certificate (HTTPS) to send push notifications.') }}
            </p>
        </div>

        {{-- Firebase Cloud Messaging (Client SDK) --}}
        <div class="white-box mb-6">
            <div class="flex justify-between items-center flex-wrap gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-500/10 flex items-center justify-center">
                        <i class="ph ph-browser text-orange-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ __('Web App Configuration') }}</p>
                        <p class="text-sm text-neutral-400">{{ __('Paste the firebaseConfig from Firebase Console') }}
                        </p>
                    </div>
                </div>
                @if ($hasClientConfig)
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-success/10 text-success text-xs">
                        <i class="ph ph-check-circle"></i>{{ __('Configured') }}
                    </span>
                @endif
            </div>

            <form action="{{ route('admin.settings.notification.services.store', 'broadcast') }}" method="POST"
                class="space-y-4 form-submit-add" id="client-config-form">
                @csrf

                {{-- JSON Input --}}
                <div>
                    <label for="client_config_json"
                        class="block text-sm font-medium mb-2">{{ __('Firebase Config JSON') }}</label>
                    <div class="relative">
                        <textarea name="client_config_json" id="client_config_json" rows="10"
                            class="form-textarea w-full font-mono text-sm bg-neutral-0 border border-neutral-40 rounded-lg p-4"
                            placeholder='{
  "apiKey": "AIzaSy...",
  "authDomain": "your-app.firebaseapp.com",
  "projectId": "your-project-id",
  "storageBucket": "your-app.appspot.com",
  "messagingSenderId": "123456789012",
  "appId": "1:123456789:web:abc123",
  "measurementId": "G-XXXXXXXXXX"
}'>{{ $clientDisplayConfig ? json_encode($clientDisplayConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '' }}</textarea>
                        <button type="button" onclick="formatJson()"
                            class="absolute top-2 right-2 text-xs px-2 py-1 bg-primary/10 text-primary rounded hover:bg-primary/20 transition">
                            {{ __('Format') }}
                        </button>
                    </div>
                    <p class="text-xs text-neutral-400 mt-1">
                        {{ __('Copy the firebaseConfig object from Firebase Console and paste it here.') }}</p>
                </div>

                {{-- VAPID Key Input --}}
                <div>
                    <label for="vapidKey"
                        class="block text-sm font-medium mb-2">{{ __('VAPID Key (Web Push Certificate)') }}</label>
                    <input type="text" name="vapidKey" id="vapidKey"
                        class="form-input w-full bg-neutral-0  border border-neutral-40 rounded-lg p-3"
                        placeholder="BNpM..." value="{{ data_get($clientConfig, 'vapidKey') }}" />
                    <p class="text-xs text-neutral-400 mt-1">
                        {{ __('Found in Project Settings → Cloud Messaging → Web Push certificates') }}</p>
                </div>

                <div class="flex justify-end">
                    <x-admin::primary-button type="submit">
                        <i class="ph ph-floppy-disk"></i>
                        <span>{{ __('Save Client Config') }}</span>
                    </x-admin::primary-button>
                </div>
            </form>
        </div>

        {{-- Firebase Admin SDK (Server) --}}
        <div class="white-box">
            <div class="flex justify-between items-center flex-wrap gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center">
                        <i class="ph ph-shield-check text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ __('Service Account Configuration') }}</p>
                        <p class="text-sm text-neutral-400">{{ __('Upload the JSON file downloaded from Firebase') }}
                        </p>
                    </div>
                </div>
                @if ($hasAdminConfig)
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-success/10 text-success text-xs">
                        <i class="ph ph-check-circle"></i>{{ __('Configured') }}
                    </span>
                @endif
            </div>

            <form action="{{ route('admin.settings.notification.firebase.json', 'admin') }}" method="POST"
                enctype="multipart/form-data" class="form-submit-add" id="admin-config-form">
                @csrf

                {{-- Upload Zone --}}
                <div class="mb-4">
                    <input type="file" id="admin-file-input" name="file" class="hidden"
                        accept=".json,application/json">
                    <label for="admin-file-input" id="admin-drop-zone"
                        class="block border-2 border-dashed border-neutral-40 rounded-lg p-8 text-center transition-all hover:border-primary/50 hover:bg-primary/5 cursor-pointer">
                        <i class="ph ph-cloud-arrow-up text-4xl text-neutral-400 mb-2"></i>
                        <p class="font-medium mb-1" id="admin-upload-text">
                            {{ __('Drop your Service Account JSON here') }}</p>
                        <p class="text-sm text-neutral-400" id="admin-upload-subtext">
                            {{ __('or click to browse files') }}</p>
                    </label>
                </div>

                @if ($hasAdminConfig)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-success/5 border border-success/20 mb-4">
                        <i class="ph ph-check-circle text-success"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium">{{ __('Service Account Key Configured') }}</p>
                            <p class="text-xs text-neutral-400">{{ __('Project:') }}
                                {{ data_get($adminConfig, 'project_id') }}</p>
                        </div>
                        <a href="{{ route('admin.settings.notification.firebase.json.download', 'admin') }}"
                            class="text-sm text-primary hover:underline">
                            <i class="ph ph-download-simple"></i> {{ __('Download') }}
                        </a>
                    </div>
                @endif

                <div class="flex justify-end">
                    <x-admin::primary-button type="submit">
                        <i class="ph ph-upload-simple"></i>
                        <span>{{ __('Upload Service Account') }}</span>
                    </x-admin::primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Test Notification Modal --}}
    <x-admin::modal title="Send Test Notification" modalId="test-firebase-notification">
        <form action="{{ route('admin.settings.notification.test.firebase') }}" method="POST"
            class="form-submit-add">
            @csrf
            <div class="space-y-4 mb-6">
                <x-admin::text-input-group name="title" label="Notification Title" placeholder="Hello World" />
                <x-admin::textarea-group name="message" label="Message"
                    placeholder="This is a test notification..." />
            </div>
            <div class="flex gap-3">
                <x-admin::primary-button type="submit">
                    <i class="ph ph-paper-plane-tilt"></i>
                    <span>{{ __('Send Test') }}</span>
                </x-admin::primary-button>
            </div>
        </form>
    </x-admin::modal>
</div>

@push('scripts')
    <script>
        // Format JSON in textarea
        function formatJson() {
            const textarea = document.getElementById('client_config_json');
            try {
                const json = JSON.parse(textarea.value);
                textarea.value = JSON.stringify(json, null, 2);
            } catch (e) {
                showToast('error', '{{ __('Invalid JSON format') }}');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Setup drag and drop for admin config
            const dropZone = document.getElementById('admin-drop-zone');
            const fileInput = document.getElementById('admin-file-input');
            const uploadText = document.getElementById('admin-upload-text');
            const uploadSubtext = document.getElementById('admin-upload-subtext');

            // Handle drag events
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.add('border-primary', 'bg-primary/10');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('border-primary', 'bg-primary/10');
                });
            });

            // Handle drop
            dropZone.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    updateFileDisplay(files[0]);
                }
            });

            // Handle file input change
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    updateFileDisplay(e.target.files[0]);
                }
            });

            function updateFileDisplay(file) {
                if (!file.name.endsWith('.json')) {
                    showToast('error', '{{ __('Please upload a JSON file') }}');
                    return;
                }

                uploadText.innerHTML = '<i class="ph ph-file-text text-success mr-1"></i>' + file.name;
                uploadSubtext.textContent = '{{ __('File selected. Click save to upload.') }}';
                dropZone.classList.add('border-success/50', 'bg-success/5');
                dropZone.classList.remove('border-neutral-200', 'dark:border-neutral-600');
            }
        });

        function showToast(type, message) {
            if (typeof toastr !== 'undefined') {
                toastr[type](message);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                alert(message);
            }
        }
    </script>
@endpush
