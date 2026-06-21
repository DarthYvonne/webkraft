<?php

namespace Webkraft\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Webkraft\Cms\Models\Page;

class PageController extends Controller
{
    private function base(): string
    {
        return '/'.trim((string) config('webkraft.path', 'cms'), '/');
    }

    public function index()
    {
        $pages = Page::topLevel()->with(['children' => fn ($q) => $q->ordered()])->ordered()->get();

        return view('webkraft::pages.index', compact('pages'));
    }

    public function create(Request $request)
    {
        $parentId = $request->query('parent') ? (int) $request->query('parent') : null;

        $page = new Page([
            'is_published' => false,
            'sort_order'   => (Page::max('sort_order') ?? 0) + 1,
            'parent_id'    => $parentId,
            'body'         => [],
        ]);

        return $this->form($page);
    }

    public function store(Request $request)
    {
        $page = Page::create($this->validated($request));

        return $this->afterSave($request, $page, 'Side oprettet.');
    }

    public function edit(Page $page)
    {
        return $this->form($page);
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->validated($request, $page->id));

        return $this->afterSave($request, $page, 'Side gemt.');
    }

    public function destroy(Page $page)
    {
        $page->delete(); // children cascade via nullOnDelete -> become top-level; delete them too
        return redirect($this->base().'/pages')->with('status', 'Side slettet.');
    }

    /** Persist drag-to-reorder: [{id, parent_id, sort_order}, ...]. */
    public function reorder(Request $request)
    {
        $items = $request->validate([
            'items'               => 'required|array',
            'items.*.id'          => 'required|integer',
            'items.*.parent_id'   => 'nullable|integer',
            'items.*.sort_order'  => 'required|integer',
        ])['items'];

        foreach ($items as $row) {
            Page::where('id', $row['id'])->update([
                'parent_id'  => $row['parent_id'] ?? null,
                'sort_order' => $row['sort_order'],
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function togglePublish(Page $page)
    {
        $page->update(['is_published' => ! $page->is_published]);

        return back()->with('status', $page->is_published ? 'Side publiceret.' : 'Side sat til kladde.');
    }

    private function form(Page $page)
    {
        $exclude = array_merge([$page->id], $page->children->pluck('id')->all());
        $parents = Page::topLevel()->whereNotIn('id', array_filter($exclude))->ordered()->get();

        return view('webkraft::pages.edit', compact('page', 'parents'));
    }

    private function afterSave(Request $request, Page $page, string $msg)
    {
        if ($request->boolean('save_and_view') && $page->is_published) {
            return redirect($page->url())->with('status', $msg);
        }

        return redirect($this->base().'/pages/'.$page->id.'/edit')->with('status', $msg);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'slug'            => 'nullable|string|max:255',
            'parent_id'       => 'nullable|integer|exists:webkraft_pages,id',
            'hero'            => 'nullable|string',   // JSON from the editor
            'body'            => 'nullable|string',   // JSON from the editor
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'is_published'    => 'nullable|boolean',
            'sort_order'      => 'nullable|integer',
        ]);

        $data['slug']         = $this->normalizeSlug($data['slug'] ?? '', $data['title']);
        $data['parent_id']    = ! empty($data['parent_id']) ? (int) $data['parent_id'] : null;
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order']   = $data['sort_order'] ?? 0;
        $data['hero']         = $this->decodeJson($data['hero'] ?? null);
        $data['body']         = $this->decodeJson($data['body'] ?? null) ?? [];

        $this->guardNesting($data['parent_id'], $ignoreId);
        $this->guardUniqueSlug($data['slug'], $data['parent_id'], $ignoreId);

        return $data;
    }

    private function normalizeSlug(string $slug, string $title): string
    {
        $slug = trim($slug, '/');
        return $slug !== '' ? Str::slug($slug) : Str::slug($title);
    }

    private function decodeJson(?string $json): mixed
    {
        if ($json === null || $json === '') {
            return null;
        }
        $decoded = json_decode($json, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    /** Enforce one-level nesting and prevent a page being its own ancestor. */
    private function guardNesting(?int $parentId, ?int $ignoreId): void
    {
        if (! $parentId) {
            return;
        }
        if ($parentId === $ignoreId) {
            abort(422, 'En side kan ikke være sin egen overside.');
        }
        $parent = Page::find($parentId);
        if ($parent && $parent->parent_id) {
            abort(422, 'Undersider kan kun ligge ét niveau dybt.');
        }
    }

    private function guardUniqueSlug(string $slug, ?int $parentId, ?int $ignoreId): void
    {
        $exists = Page::where('slug', $slug)
            ->where('parent_id', $parentId)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            abort(422, 'En side med samme URL findes allerede her.');
        }
    }
}
