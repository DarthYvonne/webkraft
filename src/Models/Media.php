<?php

namespace Webkraft\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'webkraft_media';

    protected $fillable = [
        'disk', 'path', 'type', 'mime', 'original_name',
        'alt', 'size', 'width', 'height',
    ];

    protected $casts = [
        'size'   => 'integer',
        'width'  => 'integer',
        'height' => 'integer',
    ];

    /** Public URL for the stored file. */
    public function url(): string
    {
        return Storage::disk($this->disk ?: config('webkraft.media_disk', 'public'))
            ->url($this->path);
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
