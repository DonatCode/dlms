<?php
// Variabel $active dikirim dari masing-masing controller/view
// (mis. 'dashboard', 'kategori', 'penulis', 'buku') untuk menandai menu aktif.
$active = $active ?? '';

function navClass(string $current, string $active): string
{
    return $current === $active ? 'active' : '';
}
?>
<nav class="dlms-sidebar">
    <div class="brand">
        📚 DLMS Admin
        <small id="dlms-admin-nama">&nbsp;</small>
    </div>
    <a href="/admin" class="<?= navClass('dashboard', $active) ?>">Dashboard</a>
    <a href="/admin/kategori" class="<?= navClass('kategori', $active) ?>">Kategori</a>
    <a href="/admin/penulis" class="<?= navClass('penulis', $active) ?>">Penulis</a>
    <a href="/admin/buku" class="<?= navClass('buku', $active) ?>">Buku</a>
    <a href="#" class="logout" onclick="dlmsLogout(); return false;">Keluar</a>
</nav>
