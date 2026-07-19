<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Detail Buku']) ?>
</head>
<body class="bg-surface">

<?= view('site/partials/navbar', ['active' => 'koleksi']) ?>

<main class="pt-[96px] pb-stack-lg px-margin-mobile md:px-margin-desktop max-w-[1200px] mx-auto">
    <nav class="flex items-center gap-2 mb-stack-md text-on-surface-variant font-label-sm text-label-sm">
        <a href="/" class="hover:text-primary">Beranda</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <a href="/koleksi" class="hover:text-primary">Koleksi</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span id="breadcrumb-judul" class="text-on-surface font-bold">Memuat...</span>
    </nav>

    <div id="detail-container" class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
        <div class="lg:col-span-4">
            <div class="book-aspect w-full bg-surface-variant rounded-xl skeleton"></div>
        </div>
        <div class="lg:col-span-8">
            <div class="skeleton h-10 w-2/3 rounded mb-4"></div>
            <div class="skeleton h-4 w-1/3 rounded mb-8"></div>
            <div class="skeleton h-32 w-full rounded"></div>
        </div>
    </div>

    <section class="mt-stack-lg">
        <h3 class="font-headline-md text-headline-md text-on-surface mb-stack-sm">Buku Terkait</h3>
        <div id="buku-terkait" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-gutter"></div>
    </section>
</main>

<?= view('site/partials/footer') ?>

<script src="/assets/site/site.js"></script>
<script>
renderNavbarAuth();
const bukuId = <?= (int) $id ?>;

async function muatDetail() {
    const container = document.getElementById('detail-container');
    try {
        const res = await apiFetch(`/buku/${bukuId}`);
        if (!res.ok) {
            container.innerHTML = '<p class="text-error col-span-full">Buku tidak ditemukan.</p>';
            return;
        }
        const buku = await res.json();

        const [resKategori, resPenulis, bookmarked] = await Promise.all([
            apiFetch(`/kategori/${buku.kategori_id}`),
            apiFetch(`/penulis/${buku.penulis_id}`),
            isBookmarked(bukuId),
        ]);
        const kategori = resKategori.ok ? await resKategori.json() : { nama: '-' };
        const penulis = resPenulis.ok ? await resPenulis.json() : { nama: '-' };

        document.getElementById('breadcrumb-judul').textContent = buku.judul;
        document.title = buku.judul + ' | Perpustakaan Online';

        container.innerHTML = `
            <div class="lg:col-span-4 flex flex-col items-center lg:items-start gap-stack-md">
                <div class="w-full max-w-[320px] lg:max-w-full aspect-[2/3] bg-surface-container-high rounded-xl overflow-hidden custom-shadow border border-outline-variant">
                    <img class="w-full h-full object-cover" src="${coverUrl(buku.cover)}" alt="Cover ${escapeHtml(buku.judul)}">
                </div>
                <div class="flex flex-col w-full gap-3">
                    <a href="/buku/${buku.id}/baca" class="flex items-center justify-center gap-2 w-full bg-primary text-on-primary font-label-md text-label-md py-4 rounded-lg hover:shadow-lg transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined">menu_book</span> Baca Sekarang
                    </a>
                    <div class="grid grid-cols-2 gap-3">
                        <button id="btn-unduh" class="flex items-center justify-center gap-2 w-full border border-primary text-primary font-label-md text-label-md py-3 rounded-lg hover:bg-surface-container-low transition-colors">
                            <span class="material-symbols-outlined">download</span> Unduh PDF
                        </button>
                        <button id="btn-bookmark" class="flex items-center justify-center gap-2 w-full border border-outline text-on-surface-variant font-label-md text-label-md py-3 rounded-lg hover:bg-surface-container-low transition-colors">
                            <span id="ikon-bookmark" class="material-symbols-outlined">bookmark</span> <span id="label-bookmark">Simpan</span>
                        </button>
                    </div>
                    ${dlmsGetToken() ? '' : '<p class="text-label-sm text-on-surface-variant text-center">*Masuk/daftar dulu untuk mengunduh & menyimpan bookmark.</p>'}
                </div>
            </div>
            <div class="lg:col-span-8 flex flex-col gap-stack-md">
                <div class="space-y-2">
                    <h1 class="font-headline-xl text-headline-xl text-on-surface">${escapeHtml(buku.judul)}</h1>
                    <a href="/penulis?penulis_id=${penulis.id}" class="font-headline-md text-headline-md text-primary font-medium">${escapeHtml(penulis.nama)}</a>
                </div>
                <div class="flex flex-wrap items-center gap-6 text-on-surface-variant">
                    <a href="/koleksi?kategori_id=${kategori.id}" class="flex items-center gap-2 hover:text-primary">
                        <span class="material-symbols-outlined text-[20px]">category</span>
                        <span class="font-label-md text-label-md">${escapeHtml(kategori.nama)}</span>
                    </a>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                        <span class="font-label-md text-label-md">${escapeHtml(buku.tahun_terbit ?? '-')}</span>
                    </div>
                </div>
                <div class="h-px bg-outline-variant w-full"></div>
                <div class="space-y-4">
                    <h3 class="font-headline-md text-headline-md text-on-surface">Deskripsi</h3>
                    <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">${escapeHtml(buku.deskripsi || 'Belum ada deskripsi untuk buku ini.')}</p>
                </div>
                <div class="bg-surface-container-low rounded-xl p-stack-md border border-outline-variant">
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-6">Detail Buku</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-y-6 gap-x-gutter">
                        <div class="space-y-1"><p class="text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">Penulis</p><p class="font-body-md text-body-md font-semibold">${escapeHtml(penulis.nama)}</p></div>
                        <div class="space-y-1"><p class="text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">Kategori</p><p class="font-body-md text-body-md font-semibold">${escapeHtml(kategori.nama)}</p></div>
                        <div class="space-y-1"><p class="text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">Tahun Terbit</p><p class="font-body-md text-body-md font-semibold">${escapeHtml(buku.tahun_terbit ?? '-')}</p></div>
                        <div class="space-y-1"><p class="text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">Tipe File</p><p class="font-body-md text-body-md font-semibold">PDF</p></div>
                    </div>
                </div>
            </div>`;

        document.getElementById('btn-unduh').addEventListener('click', () => downloadBuku(buku.id, buku.judul));

        let sedangBookmark = bookmarked;
        renderTombolBookmark(sedangBookmark);
        document.getElementById('btn-bookmark').addEventListener('click', () => {
            toggleBookmark(buku.id, sedangBookmark, (baru) => {
                sedangBookmark = baru;
                renderTombolBookmark(baru);
            });
        });

        muatBukuTerkait(buku.kategori_id, buku.id);
    } catch (e) {
        container.innerHTML = '<p class="text-error col-span-full">Gagal memuat detail buku.</p>';
    }
}

function renderTombolBookmark(aktif) {
    const ikon = document.getElementById('ikon-bookmark');
    const label = document.getElementById('label-bookmark');
    if (!ikon) return;
    ikon.style.fontVariationSettings = aktif ? "'FILL' 1" : "'FILL' 0";
    label.textContent = aktif ? 'Tersimpan' : 'Simpan';
}

async function muatBukuTerkait(kategoriId, kecualiId) {
    const wrap = document.getElementById('buku-terkait');
    try {
        const res = await apiFetch(`/buku?kategori_id=${kategoriId}`);
        const buku = res.ok ? await res.json() : [];
        const terkait = buku.filter(b => Number(b.id) !== Number(kecualiId)).slice(0, 6);

        if (!terkait.length) {
            wrap.innerHTML = '<p class="text-on-surface-variant col-span-full">Belum ada buku terkait di kategori ini.</p>';
            return;
        }

        wrap.innerHTML = terkait.map(b => `
            <a href="/buku/${b.id}" class="group cursor-pointer">
                <div class="book-aspect w-full bg-surface-variant rounded-lg mb-2 overflow-hidden card-hover transition-all border border-outline-variant">
                    <img class="w-full h-full object-cover" src="${coverUrl(b.cover)}" alt="Cover ${escapeHtml(b.judul)}">
                </div>
                <h4 class="font-body-md text-body-md text-on-surface truncate">${escapeHtml(b.judul)}</h4>
            </a>`).join('');
    } catch (e) {
        wrap.innerHTML = '';
    }
}

muatDetail();
</script>
</body>
</html>
