<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Baca Buku']) ?>
</head>
<body class="bg-surface-dim h-screen overflow-hidden">

<div class="h-screen flex flex-col">
    <!-- Top Reader Bar -->
    <header class="h-[64px] flex items-center justify-between px-4 md:px-6 bg-surface-container-lowest border-b border-outline-variant z-10">
        <div class="flex items-center gap-4 min-w-0">
            <a href="#" id="btn-kembali" class="flex items-center gap-1 text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
                <span class="hidden sm:inline font-label-md text-label-md">Kembali</span>
            </a>
            <div class="h-6 w-px bg-outline-variant hidden sm:block"></div>
            <h1 id="judul-buku" class="font-label-md text-label-md text-on-surface truncate">Memuat...</h1>
        </div>
        <div class="flex items-center gap-2">
            <span id="badge-mode" class="hidden sm:inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant text-label-sm font-label-sm">
                <span class="material-symbols-outlined text-[16px]">visibility</span> Mode Baca Online
            </span>
            <button id="btn-unduh" class="flex items-center gap-2 bg-primary text-on-primary px-4 py-2 rounded-lg font-label-md text-label-md hover:opacity-90 transition-all">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span class="hidden sm:inline">Unduh PDF</span>
            </button>
        </div>
    </header>

    <!-- Viewer Area -->
    <main class="flex-grow relative bg-surface-dim">
        <div id="viewer-loading" class="absolute inset-0 flex items-center justify-center text-on-surface-variant">
            Memuat berkas PDF...
        </div>
        <iframe id="pdf-frame" class="w-full h-full hidden" title="Pembaca PDF"></iframe>
    </main>
</div>

<script src="/assets/site/site.js"></script>
<script>
renderNavbarAuth();
const bukuId = <?= (int) $id ?>;
document.getElementById('btn-kembali').addEventListener('click', (e) => { e.preventDefault(); window.location.href = '/buku/' + bukuId; });

async function muatViewer() {
    try {
        const res = await apiFetch(`/buku/${bukuId}`);
        if (!res.ok) {
            document.getElementById('viewer-loading').textContent = 'Buku tidak ditemukan.';
            return;
        }
        const buku = await res.json();

        document.title = 'Baca ' + buku.judul + ' | Perpustakaan Online';
        document.getElementById('judul-buku').textContent = buku.judul;
        document.getElementById('btn-unduh').addEventListener('click', () => downloadBuku(buku.id, buku.judul));

        const frame = document.getElementById('pdf-frame');
        // Baca online memakai viewer PDF bawaan browser lewat <iframe>, mengarah
        // langsung ke file publik di /uploads/pdf/ — TIDAK memerlukan login,
        // beda dengan tombol "Unduh PDF" yang memanggil endpoint api/buku/{id}/download
        // (wajib login, lihat site.js -> downloadBuku()).
        frame.src = '/uploads/pdf/' + encodeURIComponent(buku.file_pdf) + '#toolbar=1&view=FitH';
        frame.addEventListener('load', () => {
            document.getElementById('viewer-loading').classList.add('hidden');
            frame.classList.remove('hidden');
        });
    } catch (e) {
        document.getElementById('viewer-loading').textContent = 'Gagal memuat buku.';
    }
}

muatViewer();
</script>
</body>
</html>
