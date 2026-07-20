<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Buku Saya']) ?>
</head>
<body class="bg-surface">

<?= view('site/partials/navbar', ['active' => '']) ?>
<?= view('site/partials/sidebar-account', ['active' => 'buku-saya']) ?>

<main class="lg:ml-[240px] pt-[96px] pb-stack-lg px-margin-mobile md:px-margin-desktop">
    <div class="max-w-[1000px]">
        <header class="mb-stack-lg">
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Buku Saya</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Buku yang pernah Anda unduh, dikumpulkan di sini.</p>
        </header>

        <div id="buku-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-gutter">
            <div class="skeleton book-aspect rounded-lg"></div>
            <div class="skeleton book-aspect rounded-lg"></div>
            <div class="skeleton book-aspect rounded-lg"></div>
            <div class="skeleton book-aspect rounded-lg"></div>
        </div>
    </div>
</main>

<script src="/assets/site/site.js"></script>
<script>
if (siteRequireLogin()) {
    renderNavbarAuth();

    (async () => {
        const grid = document.getElementById('buku-grid');
        try {
            const res = await apiFetch('/buku-saya');
            const buku = res.ok ? await res.json() : [];

            if (!buku.length) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <p class="text-on-surface-variant mb-4">Anda belum mengunduh buku apa pun.</p>
                        <a href="/koleksi" class="inline-block bg-primary text-on-primary px-6 py-3 rounded-lg font-label-md text-label-md hover:opacity-90 transition-all">Jelajahi Koleksi</a>
                    </div>`;
                return;
            }

            grid.innerHTML = buku.map(b => `
                <a href="/buku/${b.id}" class="group cursor-pointer">
                    <div class="book-aspect w-full bg-surface-variant rounded-lg mb-3 overflow-hidden card-hover transition-all border border-outline-variant">
                        <img class="w-full h-full object-cover" src="${coverUrl(b.cover)}" alt="Cover ${escapeHtml(b.judul)}">
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-surface truncate">${escapeHtml(b.judul)}</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">${escapeHtml(b.tahun_terbit ?? '')}</p>
                </a>`).join('');
        } catch (e) {
            grid.innerHTML = '<p class="text-error col-span-full">Gagal memuat data buku saya.</p>';
        }
    })();
}
</script>
</body>
</html>
