<?php

namespace App\Http\Controllers\Admin\HomeSection;

use App\Enums\HomeSectionLayoutEnum;
use App\Enums\HomeSectionSourceEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeSection\HomeSectionRequest;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use App\Repositories\HomeSectionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeSectionController extends Controller
{
    public function __construct(private readonly HomeSectionRepository $repository) {}

    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Create New Section'),
                'icon' => 'ph ph-plus',
                'type' => 'link',
                'link' => route('admin.home-sections.create'),
            ],
        ];

        $sections = $this->repository->allOrdered();

        $columns = [
            [
                'label' => '',
                'header_class' => 'w-10',
                'render' => fn (HomeSection $s) => '<button type="button" data-id="'.$s->id.'" '
                    .'class="home-section-drag-handle cursor-grab text-gray-400 hover:text-primary" title="'.__('Drag to reorder').'">'
                    .'<i class="ph ph-dots-six-vertical text-xl"></i></button>',
            ],
            [
                'label' => __('Title'),
                'render' => function (HomeSection $s) {
                    $title = $s->title ?: ($s->category?->name ?? $s->source->label());

                    return '<p class="s-text font-medium">'.e($title).'</p>'
                        .($s->subtitle ? '<span class="text-xs text-gray-400">'.e($s->subtitle).'</span>' : '');
                },
            ],
            [
                'label' => __('Source'),
                'render' => function (HomeSection $s) {
                    $target = match ($s->source) {
                        HomeSectionSourceEnum::CATEGORY => $s->category?->name ? ' · '.e($s->category->name) : '',
                        HomeSectionSourceEnum::PRODUCTS => ' · '.count($s->product_ids ?? []).' '.__('products'),
                        default => '',
                    };

                    return '<span class="status '.$s->source->color().' capitalize">'.__($s->source->label()).'</span>'
                        .'<span class="text-xs text-gray-400">'.$target.'</span>';
                },
            ],
            [
                'label' => __('Layout'),
                'render' => fn (HomeSection $s) => '<span class="capitalize">'.__($s->layout->label()).'</span>',
            ],
            [
                'label' => __('Limit'),
                'render' => fn (HomeSection $s) => '<span>'.$s->product_limit.'</span>',
            ],
            [
                'label' => __('Visible'),
                'render' => function (HomeSection $s) {
                    $checked = $s->is_active ? 'checked' : '';

                    return '<label class="toggle-label inline-flex"><input type="checkbox" '.$checked.' '
                        .'class="home-section-toggle sr-only peer" data-url="'.route('admin.home-sections.toggle-status', $s->id).'" />'
                        .'<div class="bg peer-checked:!bg-primary/10"></div>'
                        .'<span class="text-bg peer-checked:!bg-primary peer-checked:translate-x-full"></span></label>';
                },
            ],
            [
                'label' => __('Action'),
                'header_class' => 'flex justify-end',
                'render' => function (HomeSection $s) {
                    $action_buttons = [
                        [
                            'label' => __('Edit'),
                            'icon' => 'ph ph-pencil',
                            'type' => 'link',
                            'href' => route('admin.home-sections.edit', $s->id),
                        ],
                        [
                            'label' => __('Delete'),
                            'icon' => 'ph ph-trash',
                            'type' => 'delete',
                            'href' => route('admin.home-sections.destroy', $s->id),
                        ],
                    ];

                    return view('admin.components.table-action', compact('action_buttons'))->render();
                },
            ],
        ];

        return view('admin.pages.home-sections.index', compact('buttons', 'sections', 'columns'));
    }

    public function create()
    {
        adminUserHasPermission(permission: 'create');

        return view('admin.pages.home-sections.create', $this->formData());
    }

    public function store(HomeSectionRequest $request)
    {
        adminUserHasPermission(permission: 'create');

        try {
            DB::beginTransaction();

            HomeSection::create($request->payload());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json([
            'message' => __('Home section created successfully'),
            'redirect' => route('admin.home-sections.index'),
        ]);
    }

    public function edit(HomeSection $homeSection)
    {
        adminUserHasPermission(permission: 'edit');

        return view('admin.pages.home-sections.edit', $this->formData($homeSection));
    }

    public function update(HomeSectionRequest $request, HomeSection $homeSection)
    {
        adminUserHasPermission(permission: 'edit');

        try {
            DB::beginTransaction();

            $homeSection->update($request->payload());

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => $th->getMessage()], 400);
        }

        return response()->json([
            'message' => __('Home section updated successfully'),
            'redirect' => route('admin.home-sections.index'),
        ]);
    }

    public function destroy(HomeSection $homeSection)
    {
        adminUserHasPermission(permission: 'delete');

        $homeSection->delete();

        return response()->json(['message' => __('Home section deleted successfully')]);
    }

    /**
     * Toggle a single section's visibility (used by the index list switch).
     */
    public function toggleStatus(HomeSection $homeSection)
    {
        adminUserHasPermission(permission: 'edit');

        $homeSection->update(['is_active' => ! $homeSection->is_active]);

        return response()->json([
            'message' => $homeSection->is_active
                ? __('Section is now visible')
                : __('Section is now hidden'),
        ]);
    }

    /**
     * Persist the drag-and-drop order from the index list.
     */
    public function reorder(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:home_sections,id'],
        ])['ids'];

        $this->repository->reorder($ids);

        return response()->json(['message' => __('Section order updated')]);
    }

    /**
     * Shared data for the create/edit forms.
     *
     * @return array<string, mixed>
     */
    private function formData(?HomeSection $homeSection = null): array
    {
        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-back',
                'type' => 'link',
                'link' => route('admin.home-sections.index'),
            ],
        ];

        $categories = Category::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        // Products available to hand-pick, grouped by category in the picker.
        // Pre-load the section's current picks even if they fell out of the
        // "active" set, so the form stays accurate.
        $selectedProductIds = $homeSection?->product_ids ?? [];

        $products = Product::query()
            ->where(fn ($q) => $q->where('is_active', true)->orWhereIn('id', $selectedProductIds))
            ->with('category:id,name')
            ->orderBy('name')
            ->limit(500)
            ->get(['id', 'name', 'category_id']);

        // The current selection, in the admin-defined order, ready to render as
        // draggable chips.
        $byId = $products->keyBy('id');
        $selectedProducts = collect($selectedProductIds)
            ->map(fn ($id) => $byId->get($id))
            ->filter()
            ->values();

        return [
            'buttons' => $buttons,
            'homeSection' => $homeSection,
            'categories' => $categories,
            'products' => $products,
            'selectedProducts' => $selectedProducts,
            'selectedProductIds' => $selectedProductIds,
            'sourceOptions' => HomeSectionSourceEnum::options(),
            'layoutOptions' => HomeSectionLayoutEnum::options(),
        ];
    }
}
