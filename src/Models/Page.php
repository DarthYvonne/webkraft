<?php

namespace Webkraft\Cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $table = 'webkraft_pages';

    protected $fillable = [
        'parent_id', 'title', 'slug', 'hero', 'body',
        'seo_title', 'seo_description', 'seo_image',
        'is_published', 'sort_order',
    ];

    protected $casts = [
        'hero'         => 'array',
        'body'         => 'array',
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
        'parent_id'    => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->ordered();
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('title');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function scopeTopLevel(Builder $q): Builder
    {
        return $q->whereNull('parent_id');
    }

    /** Full public path, honouring one level of nesting (parent/child). */
    public function url(): string
    {
        $self = trim($this->slug, '/');

        if ($this->parent_id && $this->parent) {
            $parent = trim($this->parent->slug, '/');
            return '/'.($parent !== '' ? "{$parent}/{$self}" : $self);
        }

        return $self === '' ? '/' : '/'.$self;
    }
}
