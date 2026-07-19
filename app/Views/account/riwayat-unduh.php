<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Riwayat Unduh']) ?>
</head>
<body class="bg-surface">

<?= view('site/partials/navbar', ['active' => '']) ?>
<?= view('site/partials/sidebar-account', ['active' => 'riwayat-unduh']) ?>

<main class="lg:ml-[240px] pt-[96px] pb-stack-lg px-margin-mobile md:px-margin-desktop">
    <div class="max-w-[1000px]">
        <header class="mb-stack-lg">
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Riwayat Unduh</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Catatan setiap kali Anda mengunduh buku, dari yang terbaru.</p>
        </header>

        <div class="bg-white border border-outline-variant rounded-xl overflow-hidden">
            <table class="w-full">
                <thead class="bg-surface-container-low">
                    <tr>
                        <th class="text-left px-6 py-3 font-label-sm text-label-sm text-on-surface-variant">Buku</th>
                        <th class="text-left px-6 py-3 font-label-sm text-label-sm text-on-surface-variant hidden sm:table-cell">Penulis</th>
                        <th class="text-left px-6 py-3 font-label-sm text-label-sm text-on-surface-variant">Tanggal Unduh</th>
                    </tr>
                </thead>
                <tbody id="tabel-riwayat">
                    <tr><td colspan="3" class="text-center text-on-surface-variant py-8">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="/assets/site/site.js"></script>
<script>
if (siteRequireLogin()) {
    renderNavbarAuth();

    (async () => {
        const tbody = document.getElementById('tabel-riwayat');
        try {
            const res = await apiFetch('/riwayat-unduh');
            const riwayat = res.ok ? await res.json() : [];

            if (!riwayat.length) {
                tbody.innerHTML = `<tr><td colspan="3" class="text-center py-12">
                    <p class="text-on-surface-variant mb-4">Anda belum pernah mengunduh buku.</p>
                    <a href="/koleksi" class="inline-block bg-primary text-on-primary px-6 py-3 rounded-lg font-label-md text-label-md hover:opacity-90 transition-all">Jelajahi Koleksi</a>
                </td></tr>`;
                return;
            }

            tbody.innerHTML = riwayat.map(r => `
                <tr class="border-t border-outline-variant hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-3">
                        <a href="/buku/${r.buku_id}" class="flex items-center gap-3 group">
                            <img class="w-10 h-14 object-cover rounded border border-outline-variant" src="${coverUrl(r.cover)}" alt="Cover ${escapeHtml(r.judul)}">
                            <span class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors">${escapeHtml(r.judul)}</span>
                        </a>
                    </td>
                    <td class="px-6 py-3 font-body-sm text-body-sm text-on-surface-variant hidden sm:table-cell">${escapeHtml(r.penulis)}</td>
                    <td class="px-6 py-3 font-body-sm text-body-sm text-on-surface-variant">${formatTanggal(r.tanggal_unduh)}</td>
                </tr>`).join('');
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-error py-8">Gagal memuat riwayat unduh.</td></tr>';
        }
    })();
}
</script>
</body>
</html>
