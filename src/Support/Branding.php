<?php

namespace Webkraft\Cms\Support;

use Webkraft\Cms\Models\Setting;

class Branding
{
    /** Theme tokens: DB settings override config defaults. */
    public static function tokens(): array
    {
        $defaults = array_merge([
            'name'      => config('app.name'),
            'logo'      => null,
            'primary'   => '#4f46e5',
            'container' => '72rem',
        ], (array) config('webkraft.brand', []));

        return [
            'name'      => Setting::get('brand_name')      ?: $defaults['name'],
            'logo'      => Setting::get('brand_logo')      ?: $defaults['logo'],
            'primary'   => Setting::get('brand_primary')   ?: $defaults['primary'],
            'container' => Setting::get('brand_container') ?: $defaults['container'],
        ];
    }

    public static function contactEmail(): ?string
    {
        return Setting::get('contact_email')
            ?: config('webkraft.contact_email')
            ?: config('mail.from.address');
    }
}
