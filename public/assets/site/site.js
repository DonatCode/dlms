/**
 * Helper bersama untuk situs publik (mode tanpa login & mode login).
 * Memakai KUNCI localStorage yang SAMA dengan panel admin (dlms_token,
 * dlms_role, dlms_nama) supaya satu sistem login berlaku untuk admin
 * maupun user biasa — sesuai desain REST API yang sudah ada (api/login
 * berlaku untuk semua role, api/register selalu membuat role 'user').
 */

const API_BASE = '/api';

function dlmsGetToken() { return localStorage.getItem('dlms_token'); }
function dlmsGetRole() { return localStorage.getItem('dlms_role'); }
function dlmsGetNama() { return localStorage.getItem('dlms_nama') || ''; }

function dlmsSaveSession(token, role, nama) {
    localStorage.setItem('dlms_token', token);
    localStorage.setItem('dlms_role', role);
    localStorage.setItem('dlms_nama', nama || '');
}

function siteLogout() {
    localStorage.removeItem('dlms_token');
    localStorage.removeItem('dlms_role');
    localStorage.removeItem('dlms_nama');
    window.location.href = '/';
}

/**
 * Guard client-side untuk halaman AKUN (dashboard, buku-saya, bookmark,
 * riwayat-unduh): kalau belum login, redirect ke /login. Ini hanya untuk
 * UX; penegakan sesungguhnya ada di filter 'jwt' pada endpoint api/*.
 */
function siteRequireLogin() {
    if (!dlmsGetToken()) {
        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
        return false;
    }
    return true;
}

/** Dipanggil di semua halaman publik untuk menampilkan navbar sesuai status login. */
function renderNavbarAuth() {
    const guestEl = document.getElementById('nav-guest');
    const userEl = document.getElementById('nav-user');
    if (!guestEl || !userEl) return;

    if (dlmsGetToken()) {
        guestEl.classList.add('hidden');
        userEl.classList.remove('hidden');
        userEl.classList.add('flex');
        const namaEl = document.getElementById('nav-user-nama');
        if (namaEl) namaEl.textContent = dlmsGetNama() || 'Pengguna';
    } else {
        userEl.classList.add('hidden');
        guestEl.classList.remove('hidden');
        guestEl.classList.add('flex');
    }

    const sidebarNama = document.getElementById('sidebar-user-nama');
    if (sidebarNama) sidebarNama.textContent = dlmsGetNama() || 'Pengguna';
}

async function apiFetch(path, options = {}) {
    options.headers = options.headers || {};
    const token = dlmsGetToken();
    if (token) options.headers['Authorization'] = 'Bearer ' + token;

    const res = await fetch(API_BASE + path, options);
    if (res.status === 401) {
        // Token kedaluwarsa/tidak valid: bersihkan sesi, tapi jangan paksa
        // redirect di halaman publik (biar tetap bisa browsing sebagai tamu).
        localStorage.removeItem('dlms_token');
        localStorage.removeItem('dlms_role');
        localStorage.removeItem('dlms_nama');
    }
    return res;
}

async function apiErrorMessage(res, fallback = 'Terjadi kesalahan') {
    try {
        const body = await res.json();
        if (Array.isArray(body?.messages)) return body.messages.join(', ');
        if (typeof body?.messages === 'object' && body?.messages !== null) return Object.values(body.messages).join(', ');
        return body?.message || fallback;
    } catch (e) {
        return fallback;
    }
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}

function showAlert(containerId, message, type = 'danger') {
    const el = document.getElementById(containerId);
    if (!el) return;
    const color = type === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200';
    el.innerHTML = `<div class="border ${color} rounded-lg px-4 py-3 text-body-sm mb-4">${message}</div>`;
}

function formatTanggal(str) {
    if (!str) return '-';
    const d = new Date(str.replace(' ', 'T'));
    if (isNaN(d)) return str;
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function coverUrl(cover) {
    return '/uploads/covers/' + encodeURIComponent(cover);
}

/**
 * Mengunduh PDF buku. Kalau belum login, diarahkan ke halaman login dulu.
 * Request pakai fetch (bukan window.location) supaya header Authorization
 * ikut terkirim — navigasi browser biasa tidak bisa membawa header custom.
 */
async function downloadBuku(bukuId, judul = 'buku') {
    if (!dlmsGetToken()) {
        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
        return;
    }

    try {
        const res = await apiFetch(`/buku/${bukuId}/download`);
        if (!res.ok) {
            alert(await apiErrorMessage(res, 'Gagal mengunduh buku'));
            return;
        }
        const blob = await res.blob();
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = judul.replace(/[^A-Za-z0-9\- ]/g, '') + '.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    } catch (e) {
        alert('Gagal mengunduh buku. Pastikan Anda masih login.');
    }
}

/** Cek status bookmark buku tertentu bagi user yang sedang login. */
async function isBookmarked(bukuId) {
    if (!dlmsGetToken()) return false;
    try {
        const res = await apiFetch('/bookmark');
        if (!res.ok) return false;
        const data = await res.json();
        return data.some(b => Number(b.id) === Number(bukuId));
    } catch (e) {
        return false;
    }
}

/** Tambah/hapus bookmark, lalu jalankan onChange(bookmarked:boolean) untuk update tampilan tombol. */
async function toggleBookmark(bukuId, sedangBookmark, onChange) {
    if (!dlmsGetToken()) {
        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
        return;
    }
    try {
        const res = sedangBookmark
            ? await apiFetch(`/bookmark/${bukuId}`, { method: 'DELETE' })
            : await apiFetch('/bookmark', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ buku_id: bukuId }) });

        if (res.ok && onChange) onChange(!sedangBookmark);
    } catch (e) {
        alert('Gagal memperbarui bookmark.');
    }
}
