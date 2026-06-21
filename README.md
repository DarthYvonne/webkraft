# Webkraft

A drop-in **section + block CMS** for Laravel. One package, reusable across many
sites — each site keeps its own database and branding, they just share the code.

## What it gives you

- **`/cms` admin** — pages, media archive, settings (guarded by the host app's auth).
- **Pages** = a structural **hero** + a Notion-style **block body** + an auto sidebar.
- **Auto navigation** — top menu (with dropdowns) and per-page sidebar generated
  from the page tree; reorder by dragging.
- **Media archive** — upload images/videos once, reuse everywhere.
- **Public rendering** — published pages render by slug with a themeable layout.

## Install (per site)

1. Point the host app's Composer at the package (sibling folder):

   ```jsonc
   // composer.json
   "repositories": [
     { "type": "path", "url": "../webkraft", "options": { "symlink": true } }
   ]
   ```

2. Require it and migrate:

   ```bash
   composer require webkraft/cms:@dev
   php artisan migrate
   php artisan storage:link
   ```

3. (Recommended) publish the config and set your admin guard + branding:

   ```bash
   php artisan vendor:publish --tag=webkraft-config
   ```

   ```php
   // config/webkraft.php
   'path'       => 'cms',
   'middleware' => ['web', 'auth', 'admin'],
   ```

Laravel auto-discovers the service provider — no route-file edits needed.

## Configuration

See `config/webkraft.php`: admin `path`, `middleware`, `public_routes`,
`media_disk`, and `brand` (logo / primary color / container width).
