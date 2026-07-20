/**
 * Helper bersama untuk seluruh halaman admin.
 *
 * Semua request ke REST API CI4 (api/kategori, api/penulis, api/buku, api/login, dst)
 * dilakukan lewat fungsi apiFetch() di bawah ini, yang otomatis menyisipkan header
 * "Authorization: Bearer <token>" dari token JWT yang disimpan di localStorage
 * setelah admin login lewat POST /api/login.
 *
 * PENTING: path API di sini pakai path absolut ("/api/...") dengan asumsi virtual host
 * Laragon mengarah ke folder `public/` proyek ini (bukan ke folder root project).
 * Kalau di browser kamu tetap melihat index.php di URL, cek README.md bagian
 * "Setup Laragon" untuk cara memperbaikinya.
 */

const API_BASE = '/api';

function dlmsGetToken() {
  return localStorage.getItem('dlms_token');
}

function dlmsGetRole() {
  return localStorage.getItem('dlms_role');
}

function dlmsGetNama() {
  return localStorage.getItem('dlms_nama') || '';
}

function dlmsSaveSession(token, role, nama) {
  localStorage.setItem('dlms_token', token);
  localStorage.setItem('dlms_role', role);
  localStorage.setItem('dlms_nama', nama || '');
}

function dlmsLogout() {
  localStorage.removeItem('dlms_token');
  localStorage.removeItem('dlms_role');
  localStorage.removeItem('dlms_nama');
  window.location.href = '/admin/login';
}

/**
 * Guard di sisi client: kalau tidak ada token atau role bukan admin,
 * langsung tendang ke halaman login. Ini HANYA untuk kenyamanan UX
 * (mencegah halaman kosong berkedip sebelum redirect), bukan lapisan
 * keamanan utama. Keamanan sesungguhnya ada di filter 'jwt'+'admin'
 * pada endpoint api/* di server (app/Config/Routes.php).
 */
function dlmsRequireAdmin() {
  if (!dlmsGetToken() || dlmsGetRole() !== 'admin') {
    window.location.href = '/admin/login';
    return false;
  }
  const namaEl = document.getElementById('dlms-admin-nama');
  if (namaEl) namaEl.textContent = dlmsGetNama();
  return true;
}

/**
 * Wrapper fetch() yang otomatis menambahkan header Authorization.
 * Kalau server membalas 401 (token tidak ada/kedaluwarsa/tidak valid),
 * sesi lokal dibersihkan dan admin diarahkan kembali ke halaman login.
 */
async function apiFetch(path, options = {}) {
  options.headers = options.headers || {};
  const token = dlmsGetToken();
  if (token) {
    options.headers['Authorization'] = 'Bearer ' + token;
  }

  const res = await fetch(API_BASE + path, options);

  if (res.status === 401) {
    dlmsLogout();
    throw new Error('Sesi berakhir, silakan login ulang.');
  }

  return res;
}

/** Ambil pesan error dari body JSON REST API (format ResourceController CI4). */
async function apiErrorMessage(res, fallback = 'Terjadi kesalahan') {
  try {
    const body = await res.json();
    if (Array.isArray(body?.messages)) return body.messages.join(', ');
    if (typeof body?.messages === 'object' && body?.messages !== null) {
      return Object.values(body.messages).join(', ');
    }
    return body?.message || fallback;
  } catch (e) {
    return fallback;
  }
}

function showAlert(containerId, message, type = 'danger') {
  const el = document.getElementById(containerId);
  if (!el) return;
  el.innerHTML = `
    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
}

function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}
