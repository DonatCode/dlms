<?php $active = $active ?? ''; ?>
<aside class="fixed left-0 top-[72px] bottom-0 w-[240px] bg-surface-container-low border-r border-outline-variant flex-col pt-stack-md hidden lg:flex">
    <div class="px-6 mb-stack-md">
        <div class="bg-primary-container p-5 rounded-xl text-on-primary-container flex flex-col gap-1">
            <div class="font-label-sm text-label-sm opacity-90">Masuk sebagai</div>
            <div class="font-headline-md text-headline-md truncate" id="sidebar-user-nama">-</div>
        </div>
    </div>
    <nav class="flex flex-col gap-1">
        <a href="/dashboard" class="flex items-center gap-3 py-3 px-6 font-label-md text-label-md transition-all <?= $active === 'dashboard' ? 'text-primary font-bold bg-secondary-fixed border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">
            <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>
        <a href="/buku-saya" class="flex items-center gap-3 py-3 px-6 font-label-md text-label-md transition-all <?= $active === 'buku-saya' ? 'text-primary font-bold bg-secondary-fixed border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">
            <span class="material-symbols-outlined">menu_book</span> Buku Saya
        </a>
        <a href="/bookmark" class="flex items-center gap-3 py-3 px-6 font-label-md text-label-md transition-all <?= $active === 'bookmark' ? 'text-primary font-bold bg-secondary-fixed border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">
            <span class="material-symbols-outlined">bookmark</span> Bookmark
        </a>
        <a href="/riwayat-unduh" class="flex items-center gap-3 py-3 px-6 font-label-md text-label-md transition-all <?= $active === 'riwayat-unduh' ? 'text-primary font-bold bg-secondary-fixed border-l-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">
            <span class="material-symbols-outlined">history</span> Riwayat Unduh
        </a>
        <div class="my-4 border-t border-outline-variant mx-6"></div>
        <a href="/" class="flex items-center gap-3 py-3 px-6 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">
            <span class="material-symbols-outlined">storefront</span> Kembali ke Situs
        </a>
        <a href="#" onclick="siteLogout(); return false;" class="flex items-center gap-3 py-3 px-6 text-error hover:bg-error-container font-label-md text-label-md transition-all">
            <span class="material-symbols-outlined">logout</span> Keluar
        </a>
    </nav>
</aside>
