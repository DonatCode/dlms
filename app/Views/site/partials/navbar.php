<?php $active = $active ?? ''; ?>
<header class="fixed top-0 left-0 w-full z-50 h-[72px] flex items-center px-margin-mobile md:px-margin-desktop bg-surface-container-lowest border-b border-outline-variant">
    <div class="max-w-[1200px] w-full mx-auto flex justify-between items-center">
        <div class="flex items-center gap-12">
            <a href="/" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[28px]">menu_book</span>
                <span class="font-headline-md text-headline-md font-bold text-primary">Perpustakaan Online</span>
            </a>
            <nav class="hidden md:flex items-center gap-8">
                <a href="/" class="font-label-md text-label-md pb-1 <?= $active === 'beranda' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary transition-colors' ?>">Beranda</a>
                <a href="/koleksi" class="font-label-md text-label-md pb-1 <?= $active === 'koleksi' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary transition-colors' ?>">Koleksi</a>
                <a href="/kategori" class="font-label-md text-label-md pb-1 <?= $active === 'kategori' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary transition-colors' ?>">Kategori</a>
                <a href="/penulis" class="font-label-md text-label-md pb-1 <?= $active === 'penulis' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary transition-colors' ?>">Penulis</a>
            </nav>
        </div>

        <!-- Tampilan tamu (belum login) -->
        <div id="nav-guest" class="hidden items-center gap-4">
            <a href="/login" class="hidden md:block text-on-surface-variant hover:text-primary font-label-md text-label-md transition-colors">Masuk</a>
            <a href="/register" class="bg-primary text-on-primary px-6 py-2 rounded-lg font-label-md text-label-md hover:opacity-90 active:scale-[0.98] transition-all">Daftar</a>
        </div>

        <!-- Tampilan sudah login -->
        <div id="nav-user" class="hidden items-center gap-4">
            <span class="hidden md:inline font-label-md text-label-md text-on-surface">Halo, <span id="nav-user-nama">Pengguna</span></span>
            <div class="relative group">
                <button class="w-10 h-10 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined">account_circle</span>
                </button>
                <div class="absolute right-0 mt-1 w-52 bg-white border border-outline-variant rounded-lg shadow-lg py-2 hidden group-hover:block">
                    <a href="/dashboard" class="block px-4 py-2 text-body-sm text-on-surface hover:bg-surface-container-low">Dashboard</a>
                    <a href="/buku-saya" class="block px-4 py-2 text-body-sm text-on-surface hover:bg-surface-container-low">Buku Saya</a>
                    <a href="/bookmark" class="block px-4 py-2 text-body-sm text-on-surface hover:bg-surface-container-low">Bookmark</a>
                    <a href="/riwayat-unduh" class="block px-4 py-2 text-body-sm text-on-surface hover:bg-surface-container-low">Riwayat Unduh</a>
                    <div class="border-t border-outline-variant my-1"></div>
                    <a href="#" onclick="siteLogout(); return false;" class="block px-4 py-2 text-body-sm text-error hover:bg-error-container">Keluar</a>
                </div>
            </div>
        </div>
    </div>
</header>
