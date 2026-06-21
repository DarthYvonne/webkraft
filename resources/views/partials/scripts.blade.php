<script>
    // CSRF + upload helpers shared across Webkraft admin components.
    window.wkCsrf = () => document.querySelector('meta[name=csrf-token]')?.content;

    window.wkUpload = async (base, file, alt = null) => {
        const fd = new FormData();
        fd.append('file', file);
        if (alt) fd.append('alt', alt);
        const res = await fetch(base + '/media', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.wkCsrf(), 'Accept': 'application/json' },
            body: fd,
        });
        if (!res.ok) {
            const e = await res.json().catch(() => ({}));
            throw new Error(e.error || 'Upload fejlede.');
        }
        return (await res.json()).media;
    };

    document.addEventListener('alpine:init', () => {

        // ---- Media library page ---------------------------------------
        Alpine.data('webkraftMediaLibrary', (items, base) => ({
            items, base, search: '', filter: 'all',
            dragging: false, queue: [], editing: null, altDraft: '',

            get filtered() {
                const q = this.search.trim().toLowerCase();
                return this.items.filter(i => {
                    if (this.filter !== 'all' && i.type !== this.filter) return false;
                    if (!q) return true;
                    return (i.name || '').toLowerCase().includes(q)
                        || (i.alt || '').toLowerCase().includes(q);
                });
            },

            async upload(files) {
                for (const file of Array.from(files)) {
                    const u = { id: 'u' + Math.random().toString(36).slice(2), name: file.name };
                    this.queue.push(u);
                    try { this.items.unshift(await window.wkUpload(this.base, file)); }
                    catch (e) { alert(e.message); }
                    finally { this.queue = this.queue.filter(x => x.id !== u.id); }
                }
            },

            async remove(item) {
                if (!confirm('Slet denne fil?')) return;
                await fetch(`${this.base}/media/${item.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': window.wkCsrf(), 'Accept': 'application/json' },
                });
                this.items = this.items.filter(i => i.id !== item.id);
            },

            async saveAlt() {
                const res = await fetch(`${this.base}/media/${this.editing.id}`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': window.wkCsrf(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ alt: this.altDraft }),
                });
                const data = await res.json();
                const idx = this.items.findIndex(i => i.id === this.editing.id);
                if (idx > -1) this.items[idx] = data.media;
                this.editing = null;
            },
        }));

        // ---- Page editor ----------------------------------------------
        // Holds the page's hero + body state and serializes it to hidden
        // inputs. Block methods (Phase 4) and hero methods (Phase 5) extend
        // this object.
        Alpine.data('webkraftPageEditor', (initial) => ({
            isNew: initial.isNew,
            title: initial.title || '',
            slug: initial.slug || '',
            slugTouched: !!initial.slug,
            isPublished: !!initial.isPublished,
            hero: initial.hero || null,
            body: Array.isArray(initial.body) ? initial.body : [],

            blockTypes: [
                { type: 'text',    label: 'Tekst' },
                { type: 'heading', label: 'Overskrift' },
                { type: 'image',   label: 'Billede' },
                { type: 'gallery', label: 'Galleri' },
                { type: 'video',   label: 'Video' },
                { type: 'button',  label: 'Knap' },
                { type: 'columns', label: 'To kolonner' },
                { type: 'divider', label: 'Linje' },
                { type: 'form',    label: 'Kontaktformular' },
            ],

            slugify(s) {
                return (s || '').toString().toLowerCase()
                    .replace(/æ/g, 'ae').replace(/ø/g, 'oe').replace(/å/g, 'aa')
                    .normalize('NFKD').replace(/[̀-ͯ]/g, '')
                    .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            },
            onTitle() {
                if (!this.slugTouched) this.slug = this.slugify(this.title);
            },

            // --- block helpers (body editor) ---
            addBlock(type, at = null) {
                const block = { id: 'b' + Math.random().toString(36).slice(2), type, ...this.blockDefaults(type) };
                if (at === null) this.body.push(block);
                else this.body.splice(at, 0, block);
                return block;
            },
            blockDefaults(type) {
                return ({
                    text:    { html: '' },
                    heading: { level: 2, text: '' },
                    image:   { media: null },
                    gallery: { items: [] },
                    video:   { media: null, url: '' },
                    button:  { label: 'Knap', href: '', style: 'primary' },
                    divider: {},
                    columns: { left: '', right: '' },
                    form:    { heading: 'Kontakt os', button: 'Send' },
                })[type] || {};
            },
            removeBlock(i) { this.body.splice(i, 1); },
            moveBlock(i, dir) {
                const j = i + dir;
                if (j < 0 || j >= this.body.length) return;
                [this.body[i], this.body[j]] = [this.body[j], this.body[i]];
            },

            // drag-to-reorder (mutates the array so Alpine stays in sync)
            dragIndex: null,
            onDragStart(i) { this.dragIndex = i; },
            onDrop(i) {
                if (this.dragIndex === null || this.dragIndex === i) return (this.dragIndex = null);
                const [moved] = this.body.splice(this.dragIndex, 1);
                this.body.splice(i, 0, moved);
                this.dragIndex = null;
            },

            // rich-text inline formatting (operates on the focused editable)
            format(cmd) { document.execCommand(cmd, false, null); },
            formatLink() {
                const url = prompt('Link-URL:');
                if (url) document.execCommand('createLink', false, url);
            },

            // --- hero helpers ---
            setHeroType(type) {
                this.hero = type ? { type, ...this.heroDefaults(type) } : null;
            },
            heroDefaults(type) {
                return ({
                    image_bg: { media: null, headline: '', subhead: '', button_label: '', button_href: '', align: 'center' },
                    video_bg: { media: null, headline: '', subhead: '', button_label: '', button_href: '' },
                    split:    { media: null, side: 'right', headline: '', body: '', button_label: '', button_href: '' },
                })[type] || {};
            },

            pickMedia(opts, cb) {
                this.$store.mediaPicker.show(@js($webkraftBase ?? '/cms'), opts, cb);
            },
        }));

        // ---- Reusable media picker (modal) ----------------------------
        // Open from anywhere:
        //   $store.mediaPicker.show(base, {type:'image'}, media => { ... })
        Alpine.store('mediaPicker', {
            open: false, base: '', type: 'all', items: [], loading: false,
            search: '', cb: null, _debounce: null,

            show(base, opts, cb) {
                this.base = base;
                this.type = opts?.type || 'all';
                this.cb = cb;
                this.search = '';
                this.open = true;
                this.load();
            },
            async load() {
                this.loading = true;
                const url = new URL(this.base + '/media/list', location.origin);
                if (this.type !== 'all') url.searchParams.set('type', this.type);
                if (this.search) url.searchParams.set('q', this.search);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                this.items = (await res.json()).items;
                this.loading = false;
            },
            onSearch() {
                clearTimeout(this._debounce);
                this._debounce = setTimeout(() => this.load(), 250);
            },
            async uploadPicked(files) {
                for (const f of Array.from(files)) {
                    try { this.items.unshift(await window.wkUpload(this.base, f)); }
                    catch (e) { alert(e.message); }
                }
            },
            choose(item) { this.cb?.(item); this.close(); },
            close() { this.open = false; this.cb = null; },
        });
    });
</script>
