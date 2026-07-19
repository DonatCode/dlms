<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Beranda']) ?>
</head>
<body class="bg-surface">

<?= view('site/partials/navbar', ['active' => 'beranda']) ?>

<main class="pt-[72px]">
    <!-- Hero -->
    <section class="relative bg-surface-container-lowest overflow-hidden py-16 md:py-24">
        <div class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="font-headline-xl text-headline-xl mb-6 text-on-surface">
                    Selamat datang di <br> <span class="text-primary">BaBook</span>
                </h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant mb-8 max-w-lg">
                    Jelajahi koleksi buku digital secara gratis. Tanpa login Anda sudah bisa membaca di situs
                    masuk atau daftar untuk bisa mengunduh file PDF-nya.
                </p>
                <form id="form-hero-search" class="flex flex-col sm:flex-row gap-4 max-w-xl">
                    <div class="relative flex-grow">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
                        <input id="hero-search" class="w-full pl-12 pr-4 py-4 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all bg-white outline-none" placeholder="Cari judul, penulis, atau kategori..." type="text">
                    </div>
                    <button type="submit" class="bg-primary text-on-primary px-8 py-4 rounded-xl font-label-md text-label-md whitespace-nowrap hover:bg-primary/90 transition-colors">
                        Jelajahi Koleksi
                    </button>
                </form>
            </div>
            <div class="hidden md:flex justify-center">
                <div class="w-full aspect-square relative flex items-center justify-center">
                    <div class="absolute inset-0 bg-secondary-fixed opacity-30 rounded-full blur-3xl -z-10"></div>
                    <span class="material-symbols-outlined text-primary" style="font-size: 220px;">auto_stories</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Kategori Populer -->
    <section class="py-stack-lg bg-surface">
        <div class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="flex justify-between items-end mb-stack-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Kategori Populer</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Pilih topik bacaan yang paling Anda minati</p>
                </div>
                <a href="/kategori" class="text-primary font-label-md text-label-md hover:underline flex items-center gap-1">
                    Lihat semua <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </a>
            </div>
            <div id="kategori-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-gutter">
                <div class="skeleton h-32 rounded-xl"></div>
                <div class="skeleton h-32 rounded-xl"></div>
                <div class="skeleton h-32 rounded-xl"></div>
            </div>
        </div>
    </section>

    <!-- Buku Terbaru -->
    <section class="py-stack-lg">
        <div class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="flex justify-between items-end mb-stack-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Buku Terbaru</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Koleksi buku teranyar yang baru saja ditambahkan</p>
                </div>
                <a href="/koleksi" class="text-primary font-label-md text-label-md hover:underline flex items-center gap-1">
                    Lihat semua <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </a>
            </div>
            <div id="buku-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-gutter">
                <div class="skeleton book-aspect rounded-lg"></div>
                <div class="skeleton book-aspect rounded-lg"></div>
                <div class="skeleton book-aspect rounded-lg"></div>
                <div class="skeleton book-aspect rounded-lg"></div>
            </div>
        </div>
    </section>
</main>

<?= view('site/partials/footer') ?>

<script src="/assets/site/site.js"></script>
<script>
renderNavbarAuth();

document.getElementById('form-hero-search').addEventListener('submit', (e) => {
    e.preventDefault();
    const q = document.getElementById('hero-search').value.trim();
    window.location.href = '/koleksi' + (q ? '?search=' + encodeURIComponent(q) : '');
});

const IKON_KATEGORI = ['auto_stories', 'menu_book', 'computer', 'school', 'payments', 'favorite', 'science', 'palette'];
const WARNA_KATEGORI = ['bg-purple-100 text-purple-600', 'bg-blue-100 text-blue-600', 'bg-cyan-100 text-cyan-600', 'bg-emerald-100 text-emerald-600', 'bg-orange-100 text-orange-600', 'bg-pink-100 text-pink-600'];

async function muatKategoriPopuler() {
    const grid = document.getElementById('kategori-grid');
    try {
        const [resKategori, resBuku] = await Promise.all([apiFetch('/kategori'), apiFetch('/buku')]);
        const kategori = resKategori.ok ? await resKategori.json() : [];
        const buku = resBuku.ok ? await resBuku.json() : [];

        if (!kategori.length) {
            grid.innerHTML = '<p class="text-on-surface-variant col-span-full">Belum ada kategori.</p>';
            return;
        }

        grid.innerHTML = kategori.slice(0, 6).map((k, i) => {
            const jumlah = buku.filter(b => Number(b.kategori_id) === Number(k.id)).length;
            return `
                <a href="/koleksi?kategori_id=${k.id}" class="bg-white p-6 rounded-xl border border-outline-variant flex flex-col items-center gap-4 card-hover transition-all cursor-pointer group">
                    <div class="w-12 h-12 rounded-full ${WARNA_KATEGORI[i % WARNA_KATEGORI.length]} flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">${IKON_KATEGORI[i % IKON_KATEGORI.length]}</span>
                    </div>
                    <div class="text-center">
                        <h3 class="font-label-md text-label-md text-on-surface">${escapeHtml(k.nama)}</h3>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">${jumlah} buku</p>
                    </div>
                </a>`;
        }).join('');
    } catch (e) {
        grid.innerHTML = '<p class="text-error col-span-full">Gagal memuat kategori.</p>';
    }
}

async function muatBukuTerbaru() {
    const grid = document.getElementById('buku-grid');
    try {
        const res = await apiFetch('/buku');
        const buku = res.ok ? await res.json() : [];

        if (!buku.length) {
            grid.innerHTML = '<p class="text-on-surface-variant col-span-full">Belum ada buku.</p>';
            return;
        }

        grid.innerHTML = buku.slice(0, 6).map(b => `
            <a href="/buku/${b.id}" class="group cursor-pointer">
                <div class="book-aspect w-full bg-surface-variant rounded-lg mb-4 overflow-hidden card-hover transition-all border border-outline-variant">
                    <img class="w-full h-full object-cover" src="${coverUrl(b.cover)}" alt="Cover ${escapeHtml(b.judul)}">
                </div>
                <h3 class="font-headline-md text-headline-md text-on-surface truncate">${escapeHtml(b.judul)}</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant">${escapeHtml(b.tahun_terbit ?? '')}</p>
            </a>`).join('');
    } catch (e) {
        grid.innerHTML = '<p class="text-error col-span-full">Gagal memuat buku.</p>';
    }
}

muatKategoriPopuler();
muatBukuTerbaru();
</script>
</body>
</html>
