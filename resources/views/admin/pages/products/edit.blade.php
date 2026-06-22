<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Edit Product') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.products.update', $product->id) }}" class="form-submit-edit" method="POST">
            @csrf
            @method('PUT')

            {{-- ═══════════════════════════════════════════════════════════
                 SECTION 1 · Basic Information
            ═══════════════════════════════════════════════════════════ --}}
            <section class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-0/40 dark:bg-neutral-900/30 p-5 xl:p-6 mb-6">
            <div class="mb-6 flex items-center gap-2.5">
                <span class="f-center size-8 rounded-lg bg-primary/10 text-primary shrink-0"><i class="ph ph-info text-lg"></i></span>
                <h3 class="text-sm font-semibold text-neutral-600 dark:text-neutral-200 uppercase tracking-wider">{{ __('Basic Information') }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xl:gap-6">
                <div class="md:col-span-2">
                    <x-admin::text-input-group name="name" label="{{ __('Product Name') }}" placeholder="{{ __('e.g. Fuchsia Azure Delight Cotton Three Piece') }}" :value="$product->name" :required="true" />
                </div>

                <div>
                    <x-admin::label :for="'category_id'">{{ __('Category') }}</x-admin::label>
                    <x-admin::select-option id="category_id" name="category_id" placeholder="{{ __('Select Category') }}">
                        <option value="">{{ __('Select Category') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ (int) $product->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </x-admin::select-option>
                </div>

                <div>
                    <x-admin::text-input-group name="sku" label="{{ __('SKU / Reference Code') }}" placeholder="{{ __('e.g. lbm-1008') }}" :value="$product->sku" />
                </div>

                <div>
                    <x-admin::number-input-group name="price" label="{{ __('Selling Price (৳)') }}" placeholder="0.00" :value="$product->price" />
                </div>

                <div>
                    <x-admin::number-input-group name="buying_price" label="{{ __('Buying Price / Purchase Cost (৳)') }}" placeholder="0.00" :value="$product->buying_price" />
                </div>

                <div>
                    <x-admin::number-input-group name="compare_at_price" label="{{ __('Original Price / Compare At (৳)') }}" placeholder="{{ __('Leave blank if no discount') }}" :value="$product->compare_at_price" />
                </div>

                <div>
                    <x-admin::number-input-group name="stock" label="{{ __('Stock Quantity') }}" placeholder="0" :value="$product->stock" :with_currencySymbol="false" />
                    <p class="text-xs text-neutral-400 mt-1">{{ __('Set to 0 if using variants below for stock control.') }}</p>
                </div>

                <div>
                    <x-admin::number-input-group name="shipping_cost_dhaka" label="{{ __('Delivery Charge Inside Dhaka (৳)') }}" placeholder="0.00" :value="$product->shipping_cost_dhaka" />
                </div>

                <div>
                    <x-admin::number-input-group name="shipping_cost_outside" label="{{ __('Delivery Charge Outside Dhaka (৳)') }}" placeholder="0.00" :value="$product->shipping_cost_outside" />
                </div>

                <div class="md:col-span-2 flex flex-wrap items-start gap-x-12 gap-y-4 pt-2 mt-1 border-t border-neutral-200 dark:border-neutral-700">
                    <div class="pt-4">
                        <x-admin::label :for="'is_active'">{{ __('Status') }}</x-admin::label>
                        <x-admin::switch name="is_active" id="is_active" :value="$product->is_active ? 1 : 0"
                            :types="[['label' => __('Inactive'), 'value' => 0], ['label' => __('Active'), 'value' => 1]]" />
                    </div>
                    <div class="pt-4">
                        <x-admin::label :for="'is_featured'">{{ __('Featured') }}</x-admin::label>
                        <x-admin::switch name="is_featured" id="is_featured" :value="$product->is_featured ? 1 : 0"
                            :types="[['label' => __('No'), 'value' => 0], ['label' => __('Yes'), 'value' => 1]]" />
                    </div>
                </div>

                {{-- Variant stock summary for reference --}}
                @if ($product->variants->isNotEmpty())
                    <div class="md:col-span-2">
                        <div class="rounded-xl border border-teal-200 bg-teal-50 dark:bg-teal-900/20 dark:border-teal-700 p-4">
                            <p class="text-xs font-semibold text-teal-700 dark:text-teal-400 mb-2">
                                <i class="ph ph-info mr-1"></i>{{ __('This product uses variants. Current stock by size:') }}
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($product->variants as $v)
                                    <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full bg-white dark:bg-neutral-800 border border-teal-200 dark:border-neutral-600 font-medium text-neutral-700 dark:text-neutral-200">
                                        {{ $v->name }}
                                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $v->stock <= 5 ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-700' }}">
                                            {{ $v->stock }}
                                        </span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            </section>

            {{-- ═══════════════════════════════════════════════════════════
                 SECTION 2 · Description
            ═══════════════════════════════════════════════════════════ --}}
            <section class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-0/40 dark:bg-neutral-900/30 p-5 xl:p-6 mb-6">
            <div class="mb-6 flex items-center justify-between gap-2 flex-wrap">
                <div class="flex items-center gap-2.5">
                    <span class="f-center size-8 rounded-lg bg-primary/10 text-primary shrink-0"><i class="ph ph-article text-lg"></i></span>
                    <h3 class="text-sm font-semibold text-neutral-600 dark:text-neutral-200 uppercase tracking-wider">{{ __('Product Description') }}</h3>
                </div>
                <div class="flex gap-2">
                    <button type="button" id="tpl-three-piece"
                        class="text-xs px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 border border-teal-200 hover:bg-teal-100 transition font-medium">
                        <i class="ph ph-magic-wand mr-1"></i>{{ __('Three Piece Template') }}
                    </button>
                    <button type="button" id="tpl-clear"
                        class="text-xs px-3 py-1.5 rounded-lg bg-neutral-100 text-neutral-500 border border-neutral-200 hover:bg-neutral-200 transition font-medium">
                        <i class="ph ph-eraser mr-1"></i>{{ __('Clear') }}
                    </button>
                </div>
            </div>

            <div>
                <div class="mb-4">
                    <x-admin::textarea-group name="short_description" label="{{ __('Short Description') }}" placeholder="{{ __('Brief 1-2 line summary shown on product cards') }}" :value="$product->short_description" />
                </div>
                <div>
                    <x-admin::label :for="'description'">{{ __('Full Description') }}</x-admin::label>
                    <p class="text-xs text-neutral-400 mb-2">{{ __('Use the template button above to auto-fill structured sections, then edit each one.') }}</p>
                    <x-admin::editor id="description-editor" name="description" :value="$product->description ?? ''" />
                </div>
            </div>
            </section>

            {{-- ═══════════════════════════════════════════════════════════
                 SECTION 3 · Images
            ═══════════════════════════════════════════════════════════ --}}
            <section class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-0/40 dark:bg-neutral-900/30 p-5 xl:p-6 mb-6">
            <div class="mb-6 flex items-center gap-2.5">
                <span class="f-center size-8 rounded-lg bg-primary/10 text-primary shrink-0"><i class="ph ph-image text-lg"></i></span>
                <h3 class="text-sm font-semibold text-neutral-600 dark:text-neutral-200 uppercase tracking-wider">{{ __('Product Images') }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xl:gap-6">
                <div>
                    <x-admin::label :for="'thumbnail'">{{ __('Main Thumbnail') }}</x-admin::label>
                    <p class="text-xs text-neutral-400 mb-2">{{ __('Primary image shown on product cards.') }}</p>
                    <x-admin::file-uploader name="thumbnail" id="thumbnail" :value="$product->thumbnail" />
                </div>

                <div class="md:col-span-2">
                    <x-admin::label>{{ __('Gallery Images') }}</x-admin::label>
                    <p class="text-xs text-neutral-400 mb-2">{{ __('Additional images for the product gallery (front, back, detail shots, etc.)') }}</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @php $existingImages = $product->images->pluck('image')->values(); @endphp
                        @for ($i = 0; $i < 4; $i++)
                            <x-admin::file-uploader name="images[]" id="gallery-{{ $i }}" :value="$existingImages[$i] ?? null" />
                        @endfor
                    </div>
                </div>

                <div class="md:col-span-2">
                    <x-admin::text-input-group name="video_url" label="{{ __('Product Video URL (optional)') }}"
                        :value="$product->video_url"
                        placeholder="{{ __('e.g. https://www.youtube.com/watch?v=… or a Facebook video link') }}" />
                    <p class="text-xs text-neutral-400 mt-1">{{ __('Paste a YouTube or Facebook video link. It appears in the gallery with a play icon; clicking it plays the video.') }}</p>
                </div>
            </div>
            </section>

            {{-- ═══════════════════════════════════════════════════════════
                 SECTION 4 · Variants
            ═══════════════════════════════════════════════════════════ --}}
            <section class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-0/40 dark:bg-neutral-900/30 p-5 xl:p-6 mb-6">
            <div class="mb-6 flex items-center gap-2.5">
                <span class="f-center size-8 rounded-lg bg-primary/10 text-primary shrink-0"><i class="ph ph-squares-four text-lg"></i></span>
                <h3 class="text-sm font-semibold text-neutral-600 dark:text-neutral-200 uppercase tracking-wider">{{ __('Size & Color Variants') }}</h3>
            </div>

            <div class="grid grid-cols-1 gap-4 xl:gap-6">
                @include('admin.pages.products._variants')
            </div>
            </section>

            <div class="flex items-center justify-end gap-3 mt-6 sticky bottom-0 bg-neutral-0 dark:bg-neutral-900 py-4 -mx-5 px-5 border-t border-neutral-200 dark:border-neutral-700">
                <x-admin::primary-button type="submit">
                    <i class="ph ph-floppy-disk mr-1"></i> {{ __('Save Changes') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    (function () {
        // ─── Description Template Helper ───────────────────────────────────────
        const threePieceTemplate = `<p><strong>ফেব্রিক:</strong> এই সম্পূর্ণ পোশাকটি তৈরি করা হয়েছে ১০০% প্রিমিয়াম পিওর কটন (Pure Cotton) ফেব্রিক দিয়ে। পিওর কটন হওয়ার কারণে এটি অত্যন্ত আরামদায়ক, ঘাম শোষক এবং ত্বকের জন্য বন্ধুত্বপূর্ণ।</p>
<p><strong>কামিজ:</strong> সূক্ষ্ম এমব্রয়ডারি ও আইলেট লেসের কাজ সহ আধুনিক কাটের কামিজ। হাতার নকশাটি পোশাকটিকে ক্যাজুয়াল থেকে সেমি-ফরমাল লুকে নিয়ে যায়।</p>
<p><strong>ওড়না:</strong> ডিজিটাল প্রিন্টেড ওড়না, যা কামিজের রঙের সাথে সামঞ্জস্য রেখে মাল্টি-কালার ফ্লোরাল মোটিফে ডিজাইন করা। পিওর কটন ওড়নাটি বেশ বড় ও আরামদায়ক।</p>
<p><strong>সালোয়ার:</strong> ম্যাচিং প্যান্ট কাট সালোয়ার। নিচের অংশে সূক্ষ্ম লেসের ডিটেইলিং করা হয়েছে যা পুরো সেটটিকে একটি কমপ্লিট লুক দেয়।</p>
<p><strong>উপযোগিতা:</strong> পিওর কটন ড্রেস মানেই স্বস্তি। সারাদিন অফিস, ভার্সিটি বা ঘরোয়া অনুষ্ঠানে পরে থাকার জন্য এটি একটি আদর্শ পোশাক। ধোয়ার পরেও এর উজ্জ্বলতা বজায় থাকবে।</p>
<p><strong>বিশেষ দ্রষ্টব্য:</strong> ক্যামেরার লাইটিং, ফটোগ্রাফি এবং আপনার ডিভাইসের ডিসপ্লে সেটিং-এর কারণে পণ্যের প্রকৃত রঙ এবং ছবির রঙের মধ্যে সামান্য তারতম্য হতে পারে। তবে আমরা পণ্যের আসল রঙটি ছবিতে ফুটিয়ে তোলার সর্বোচ্চ চেষ্টা করেছি।</p>`;

        function getQuillInstance() {
            const editorEl = document.getElementById('description-editor');
            return editorEl?.__quill || null;
        }

        document.getElementById('tpl-three-piece')?.addEventListener('click', function () {
            const q = getQuillInstance();
            if (q) {
                q.clipboard.dangerouslyPasteHTML(threePieceTemplate);
            } else {
                const input = document.getElementById('description-editor_input');
                if (input) {
                    input.value = threePieceTemplate;
                    input.dispatchEvent(new Event('change'));
                }
            }
        });

        document.getElementById('tpl-clear')?.addEventListener('click', function () {
            const q = getQuillInstance();
            if (q) {
                q.setContents([]);
            } else {
                const input = document.getElementById('description-editor_input');
                if (input) input.value = '';
            }
        });
    })();
    </script>
    @endpush
</x-admin-app-layout>
