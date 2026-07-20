<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Kategori']) ?>
</head>
<body class="bg-surface">

<?= view('site/partials/navbar', ['active' => 'kategori']) ?>

<main class="pt-[96px] pb-stack-lg px-margin-mobile md:px-margin-desktop max-w-[1200px] mx-auto">
    <div class="mb-stack-md">
        <h1 class="font-headline-lg text-headline-lg text-on-surface">Kategori</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Jelajahi buku berdasarkan topik yang Anda minati</p>
    </div>

    <div id="kategori-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-gutter">
        <div class="skeleton h-32 rounded-xl"></div>
        <div class="skeleton h-32 rounded-xl"></div>
        <div class="skeleton h-32 rounded-xl"></div>
        <div class="skeleton h-32 rounded-xl"></div>
    </div>
</main>

<?= view('site/partials/footer') ?>

<script src="/assets/site/site.js"></script>
<script>
renderNavbarAuth();

const IKON = ['auto_stories', 'menu_book', 'computer', 'school', 'payments', 'favorite', 'science', 'palette', 'public', 'sports_esports', 'restaurant', 'travel_explore'];
const WARNA = ['bg-purple-100 text-purple-600', 'bg-blue-100 text-blue-600', 'bg-cyan-100 text-cyan-600', 'bg-emerald-100 text-emerald-600', 'bg-orange-100 text-orange-600', 'bg-pink-100 text-pink-600'];

async function muatKategori() {
    const grid = document.getElementById('kategori-grid');
    try {
        const [resKategori, resBuku] = await Promise.all([apiFetch('/kategori'), apiFetch('/buku')]);
        const kategori = resKategori.ok ? await resKategori.json() : [];
        const buku = resBuku.ok ? await resBuku.json() : [];

        if (!kategori.length) {
            grid.innerHTML = '<p class="text-on-surface-variant col-span-full">Belum ada kategori.</p>';
            return;
        }

        grid.innerHTML = kategori.map((k, i) => {
            const jumlah = buku.filter(b => Number(b.kategori_id) === Number(k.id)).length;
            return `
                <a href="/koleksi?kategori_id=${k.id}" class="bg-white p-6 rounded-xl border border-outline-variant flex flex-col items-center gap-4 card-hover transition-all cursor-pointer group">
                    <div class="w-14 h-14 rounded-full ${WARNA[i % WARNA.length]} flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[28px]">${IKON[i % IKON.length]}</span>
                    </div>
                    <div class="text-center">
                        <h3 class="font-headline-md text-headline-md text-on-surface">${escapeHtml(k.nama)}</h3>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">${jumlah} buku</p>
                    </div>
                </a>`;
        }).join('');
    } catch (e) {
        grid.innerHTML = '<p class="text-error col-span-full">Gagal memuat kategori.</p>';
    }
}

muatKategori();
</script>
</body>
</html>
