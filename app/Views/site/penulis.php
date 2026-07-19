<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Penulis']) ?>
</head>
<body class="bg-surface">

<?= view('site/partials/navbar', ['active' => 'penulis']) ?>

<main class="pt-[96px] pb-stack-lg px-margin-mobile md:px-margin-desktop max-w-[1200px] mx-auto">
    <div class="mb-stack-md">
        <h1 class="font-headline-lg text-headline-lg text-on-surface">Penulis</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Kenali para penulis di balik koleksi buku kami</p>
    </div>

    <div id="penulis-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-gutter">
        <div class="skeleton h-40 rounded-xl"></div>
        <div class="skeleton h-40 rounded-xl"></div>
        <div class="skeleton h-40 rounded-xl"></div>
        <div class="skeleton h-40 rounded-xl"></div>
    </div>
</main>

<?= view('site/partials/footer') ?>

<script src="/assets/site/site.js"></script>
<script>
renderNavbarAuth();

function inisial(nama) {
    return nama.split(' ').map(s => s[0]).slice(0, 2).join('').toUpperCase();
}

async function muatPenulis() {
    const grid = document.getElementById('penulis-grid');
    try {
        const [resPenulis, resBuku] = await Promise.all([apiFetch('/penulis'), apiFetch('/buku')]);
        const penulis = resPenulis.ok ? await resPenulis.json() : [];
        const buku = resBuku.ok ? await resBuku.json() : [];

        if (!penulis.length) {
            grid.innerHTML = '<p class="text-on-surface-variant col-span-full">Belum ada data penulis.</p>';
            return;
        }

        grid.innerHTML = penulis.map(p => {
            const jumlah = buku.filter(b => Number(b.penulis_id) === Number(p.id)).length;
            return `
                <a href="/koleksi?penulis_id=${p.id}" class="bg-white p-6 rounded-xl border border-outline-variant flex flex-col items-center text-center gap-3 card-hover transition-all cursor-pointer">
                    <div class="w-20 h-20 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-headline-md text-headline-md font-bold">
                        ${inisial(p.nama)}
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-surface">${escapeHtml(p.nama)}</h3>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">${jumlah} buku diterbitkan</p>
                </a>`;
        }).join('');
    } catch (e) {
        grid.innerHTML = '<p class="text-error col-span-full">Gagal memuat data penulis.</p>';
    }
}

muatPenulis();
</script>
</body>
</html>
