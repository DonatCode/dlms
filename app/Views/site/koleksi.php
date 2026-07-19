<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Koleksi Buku']) ?>
</head>
<body class="bg-surface">

<?= view('site/partials/navbar', ['active' => 'koleksi']) ?>

<main class="pt-[96px] pb-stack-lg px-margin-mobile md:px-margin-desktop max-w-[1200px] mx-auto">
    <nav class="flex items-center gap-2 mb-stack-md text-on-surface-variant font-label-sm text-label-sm">
        <a href="/" class="hover:text-primary">Beranda</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-on-surface font-bold">Koleksi</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
        <!-- Filter sidebar -->
        <aside class="lg:col-span-3">
            <div class="bg-white border border-outline-variant rounded-xl p-6 sticky top-[96px]">
                <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Cari & Filter</h3>
                <div class="mb-4">
                    <label class="font-label-sm text-label-sm text-on-surface-variant block mb-1">Kata Kunci</label>
                    <input id="filter-search" type="text" class="w-full px-4 py-2 rounded-lg border border-outline-variant focus:border-primary outline-none" placeholder="Judul atau deskripsi...">
                </div>
                <div class="mb-4">
                    <label class="font-label-sm text-label-sm text-on-surface-variant block mb-1">Kategori</label>
                    <select id="filter-kategori" class="w-full px-4 py-2 rounded-lg border border-outline-variant outline-none">
                        <option value="">Semua Kategori</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="font-label-sm text-label-sm text-on-surface-variant block mb-1">Penulis</label>
                    <select id="filter-penulis" class="w-full px-4 py-2 rounded-lg border border-outline-variant outline-none">
                        <option value="">Semua Penulis</option>
                    </select>
                </div>
                <button id="btn-filter" class="w-full bg-primary text-on-primary py-3 rounded-lg font-label-md text-label-md hover:opacity-90 transition-all">Terapkan Filter</button>
            </div>
        </aside>

        <!-- Grid -->
        <div class="lg:col-span-9">
            <div class="flex justify-between items-center mb-stack-md">
                <h1 class="font-headline-lg text-headline-lg text-on-surface">Koleksi Buku</h1>
                <span id="jumlah-hasil" class="font-label-sm text-label-sm text-on-surface-variant"></span>
            </div>
            <div id="buku-grid" class="grid grid-cols-2 md:grid-cols-3 gap-gutter">
                <div class="skeleton book-aspect rounded-lg"></div>
                <div class="skeleton book-aspect rounded-lg"></div>
                <div class="skeleton book-aspect rounded-lg"></div>
            </div>
        </div>
    </div>
</main>

<?= view('site/partials/footer') ?>

<script src="/assets/site/site.js"></script>
<script>
renderNavbarAuth();

const params = new URLSearchParams(window.location.search);
document.getElementById('filter-search').value = params.get('search') || '';

async function muatOpsiFilter() {
    const [resKategori, resPenulis] = await Promise.all([apiFetch('/kategori'), apiFetch('/penulis')]);
    const kategori = resKategori.ok ? await resKategori.json() : [];
    const penulis = resPenulis.ok ? await resPenulis.json() : [];

    const selKategori = document.getElementById('filter-kategori');
    kategori.forEach(k => selKategori.insertAdjacentHTML('beforeend', `<option value="${k.id}">${escapeHtml(k.nama)}</option>`));
    if (params.get('kategori_id')) selKategori.value = params.get('kategori_id');

    const selPenulis = document.getElementById('filter-penulis');
    penulis.forEach(p => selPenulis.insertAdjacentHTML('beforeend', `<option value="${p.id}">${escapeHtml(p.nama)}</option>`));
    if (params.get('penulis_id')) selPenulis.value = params.get('penulis_id');
}

async function muatBuku() {
    const grid = document.getElementById('buku-grid');
    grid.innerHTML = '<div class="skeleton book-aspect rounded-lg"></div>'.repeat(6);

    const qs = new URLSearchParams();
    const search = document.getElementById('filter-search').value.trim();
    const kategoriId = document.getElementById('filter-kategori').value;
    const penulisId = document.getElementById('filter-penulis').value;
    if (search) qs.set('search', search);
    if (kategoriId) qs.set('kategori_id', kategoriId);
    if (penulisId) qs.set('penulis_id', penulisId);

    // Supaya bisa di-bookmark/refresh, URL browser ikut diperbarui tanpa reload halaman.
    history.replaceState(null, '', '/koleksi' + (qs.toString() ? '?' + qs.toString() : ''));

    try {
        const res = await apiFetch('/buku' + (qs.toString() ? '?' + qs.toString() : ''));
        const buku = res.ok ? await res.json() : [];

        document.getElementById('jumlah-hasil').textContent = buku.length + ' buku ditemukan';

        if (!buku.length) {
            grid.innerHTML = '<p class="text-on-surface-variant col-span-full py-12 text-center">Tidak ada buku yang cocok dengan pencarian.</p>';
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
        grid.innerHTML = '<p class="text-error col-span-full">Gagal memuat data buku.</p>';
    }
}

document.getElementById('btn-filter').addEventListener('click', muatBuku);
document.getElementById('filter-search').addEventListener('keydown', (e) => { if (e.key === 'Enter') muatBuku(); });

muatOpsiFilter().then(muatBuku);
</script>
</body>
</html>
