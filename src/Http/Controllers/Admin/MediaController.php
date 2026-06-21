<?php

namespace Webkraft\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkraft\Cms\Models\Media;

class MediaController extends Controller
{
    private const VIDEO_EXT = ['mp4', 'webm', 'ogg', 'mov'];
    private const ALLOWED   = 'jpg,jpeg,png,webp,gif,svg,avif,mp4,webm,ogg,mov';

    /** Full library page. */
    public function index()
    {
        $items = Media::latest()->get()->map(fn (Media $m) => $this->toArray($m));

        return view('webkraft::media.index', ['items' => $items]);
    }

    /** JSON feed for the reusable picker (filter by type + search). */
    public function list(Request $request)
    {
        $query = Media::query()->latest();

        if (in_array($request->query('type'), ['image', 'video'], true)) {
            $query->where('type', $request->query('type'));
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(fn ($w) => $w
                ->where('original_name', 'like', "%{$search}%")
                ->orWhere('alt', 'like', "%{$search}%"));
        }

        return response()->json([
            'items' => $query->limit(200)->get()->map(fn (Media $m) => $this->toArray($m)),
        ]);
    }

    /** Handle one or more uploads. Returns JSON when called over AJAX. */
    public function store(Request $request)
    {
        if ($error = $this->phpUploadError('file')) {
            return $request->wantsJson()
                ? response()->json(['error' => $error], 422)
                : back()->withErrors(['file' => $error]);
        }

        $request->validate([
            'file' => "required|file|mimes:".self::ALLOWED."|max:51200", // 50 MB
            'alt'  => 'nullable|string|max:255',
        ]);

        $media = $this->storeUploaded($request->file('file'), $request->input('alt'));

        return $request->wantsJson()
            ? response()->json(['media' => $this->toArray($media)])
            : back()->with('status', 'Fil uploadet.');
    }

    public function update(Request $request, Media $media)
    {
        $data = $request->validate(['alt' => 'nullable|string|max:255']);
        $media->update(['alt' => $data['alt'] ?? null]);

        return $request->wantsJson()
            ? response()->json(['media' => $this->toArray($media)])
            : back()->with('status', 'Gemt.');
    }

    public function destroy(Request $request, Media $media)
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return $request->wantsJson()
            ? response()->json(['ok' => true])
            : back()->with('status', 'Fil slettet.');
    }

    /** Move an upload onto the configured disk and record it. */
    private function storeUploaded(\Illuminate\Http\UploadedFile $file, ?string $alt): Media
    {
        $disk = config('webkraft.media_disk', 'public');
        $base = trim(config('webkraft.media_path', 'webkraft'), '/');
        $ext  = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $type = in_array($ext, self::VIDEO_EXT, true) ? 'video' : 'image';

        $folder = "{$base}/{$type}s/".date('Y/m');
        $name   = Str::random(20).'.'.$ext;
        $path   = $file->storeAs($folder, $name, $disk);

        $width = $height = null;
        if ($type === 'image' && $ext !== 'svg') {
            $dims = @getimagesize(Storage::disk($disk)->path($path));
            if ($dims) {
                [$width, $height] = $dims;
            }
        }

        return Media::create([
            'disk'          => $disk,
            'path'          => $path,
            'type'          => $type,
            'mime'          => $file->getClientMimeType(),
            'original_name' => $file->getClientOriginalName(),
            'alt'           => $alt,
            'size'          => $file->getSize(),
            'width'         => $width,
            'height'        => $height,
        ]);
    }

    private function toArray(Media $m): array
    {
        return [
            'id'     => $m->id,
            'url'    => $m->url(),
            'type'   => $m->type,
            'alt'    => $m->alt,
            'name'   => $m->original_name,
            'width'  => $m->width,
            'height' => $m->height,
            'size'   => $m->size,
        ];
    }

    /** Surface PHP-level upload failures (post_max_size etc.) that bypass validation. */
    private function phpUploadError(string $field): ?string
    {
        if (! isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_OK || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return match ($_FILES[$field]['error']) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Filen er for stor til at blive uploadet.',
            UPLOAD_ERR_PARTIAL    => 'Upload blev afbrudt. Prøv igen.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server-fejl: ingen midlertidig mappe.',
            UPLOAD_ERR_CANT_WRITE => 'Server-fejl: kunne ikke skrive filen.',
            default               => 'Upload-fejl (kode '.$_FILES[$field]['error'].').',
        };
    }
}
