<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Dashboard']) ?>
</head>
<body class="bg-surface">

<?= view('site/partials/navbar', ['active' => '']) ?>
<?= view('site/partials/sidebar-account', ['active' => 'dashboard']) ?>

<main class="lg:ml-[240px] pt-[96px] pb-stack-lg px-margin-mobile md:px-margin-desktop">
    <div class="max-w-[1000px]">
        <header class="mb-stack-lg">
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Dashboard</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Ringkasan aktivitas Anda di Perpustakaan Online.</p>
        </header>

        <section class="grid grid-cols-1 sm:grid-cols-3 gap-gutter mb-stack-lg">
            <div class="bg-white p-6 rounded-xl border border-outline-variant custom-shadow flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary-fixed text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined">library_books</span>
                </div>
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Buku Saya</p>
                    <h2 id="stat-buku-saya" class="font-headline-md text-headline-md text-on-surface">-</h2>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl border border-outline-variant custom-shadow flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-secondary-fixed text-secondary flex items-center justify-center">
                    <span class="material-symbols-outlined">bookmark</span>
                </div>
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Bookmark</p>
                    <h2 id="stat-bookmark" class="font-headline-md text-headline-md text-on-surface">-</h2>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl border border-outline-variant custom-shadow flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-tertiary-fixed text-tertiary flex items-center justify-center">
                    <span class="material-symbols-outlined">download</span>
                </div>
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Total Unduhan</p>
                    <h2 id="stat-unduhan" class="font-headline-md text-headline-md text-on-surface">-</h2>
                </div>
            </div>
        </section>

        <section>
            <div class="flex justify-between items-center mb-stack-sm">
                <h3 class="font-headline-md text-headline-md text-on-surface">Terakhir Diunduh</h3>
                <a href="/riwayat-unduh" class="font-label-md text-label-md text-primary flex items-center gap-1 hover:underline">
                    Lihat semua <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </a>
            </div>
            <div id="terakhir-unduh" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-gutter">
                <div class="skeleton book-aspect rounded-lg"></div>
                <div class="skeleton book-aspect rounded-lg"></div>
            </div>
        </section>
    </div>
</main>

<script src="/assets/site/site.js"></script>
<script>
if (siteRequireLogin()) {
    renderNavbarAuth();

    (async () => {
        try {
            const [resBukuSaya, resBookmark, resRiwayat] = await Promise.all([
                apiFetch('/buku-saya'),
                apiFetch('/bookmark'),
                apiFetch('/riwayat-unduh'),
            ]);
            const bukuSaya = resBukuSaya.ok ? await resBukuSaya.json() : [];
            const bookmark = resBookmark.ok ? await resBookmark.json() : [];
            const riwayat = resRiwayat.ok ? await resRiwayat.json() : [];

            document.getElementById('stat-buku-saya').textContent = bukuSaya.length;
            document.getElementById('stat-bookmark').textContent = bookmark.length;
            document.getElementById('stat-unduhan').textContent = riwayat.length;

            const wrap = document.getElementById('terakhir-unduh');
            if (!riwayat.length) {
                wrap.innerHTML = '<p class="text-on-surface-variant col-span-full">Anda belum pernah mengunduh buku. <a href="/koleksi" class="text-primary hover:underline">Jelajahi koleksi</a>.</p>';
                return;
            }

            const unik = [];
            const seen = new Set();
            for (const r of riwayat) {
                if (!seen.has(r.buku_id)) { seen.add(r.buku_id); unik.push(r); }
                if (unik.length >= 5) break;
            }

            wrap.innerHTML = unik.map(r => `
                <a href="/buku/${r.buku_id}" class="group cursor-pointer">
                    <div class="book-aspect w-full bg-surface-variant rounded-lg mb-2 overflow-hidden card-hover transition-all border border-outline-variant">
                        <img class="w-full h-full object-cover" src="${coverUrl(r.cover)}" alt="Cover ${escapeHtml(r.judul)}">
                    </div>
                    <h4 class="font-body-md text-body-md text-on-surface truncate">${escapeHtml(r.judul)}</h4>
                </a>`).join('');
        } catch (e) {
            document.getElementById('terakhir-unduh').innerHTML = '<p class="text-error col-span-full">Gagal memuat data.</p>';
        }
    })();
}
</script>
</body>
</html>
