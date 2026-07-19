<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buku - DLMS Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/admin/admin.css" rel="stylesheet">
</head>
<body>

<?= view('admin/partials/nav', ['active' => $active]) ?>

<main class="dlms-content">
    <div class="dlms-topbar">
        <h4 class="mb-0">Buku</h4>
        <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#modalBuku" onclick="bukaForm()">
            + Tambah Buku
        </button>
    </div>

    <div id="alert-box"></div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table dlms-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:56px">Cover</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th style="width:80px">Tahun</th>
                        <th style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel-buku">
                    <tr><td colspan="6" class="text-center text-muted py-4">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal tambah/edit buku -->
<div class="modal fade" id="modalBuku" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-buku">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBukuTitle">Tambah Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-alert-box"></div>
                    <input type="hidden" id="buku-id">

                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" id="buku-judul" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="buku-deskripsi" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" id="buku-kategori" required></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penulis</label>
                            <select class="form-select" id="buku-penulis" required></select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tahun Terbit</label>
                        <input type="number" class="form-control" id="buku-tahun" min="1900" max="2100">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cover (gambar) <span id="cover-required" class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="buku-cover" accept="image/*">
                            <div class="form-text" id="cover-existing"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">File PDF <span id="pdf-required" class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="buku-pdf" accept="application/pdf">
                            <div class="form-text" id="pdf-existing"></div>
                        </div>
                    </div>
                    <div class="form-text">Saat mengedit buku, cover/PDF cukup diisi jika ingin menggantinya.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/admin/admin.js"></script>
<script>
const modalBuku = () => new bootstrap.Modal(document.getElementById('modalBuku'));

let daftarKategori = [];
let daftarPenulis = [];

function petaNama(list) {
    return Object.fromEntries(list.map(item => [item.id, item.nama]));
}

async function muatOpsi() {
    const [resKategori, resPenulis] = await Promise.all([apiFetch('/kategori'), apiFetch('/penulis')]);
    daftarKategori = resKategori.ok ? await resKategori.json() : [];
    daftarPenulis  = resPenulis.ok ? await resPenulis.json() : [];

    document.getElementById('buku-kategori').innerHTML =
        daftarKategori.map(k => `<option value="${k.id}">${escapeHtml(k.nama)}</option>`).join('');
    document.getElementById('buku-penulis').innerHTML =
        daftarPenulis.map(p => `<option value="${p.id}">${escapeHtml(p.nama)}</option>`).join('');
}

async function muatBuku() {
    const tbody = document.getElementById('tabel-buku');
    try {
        const res = await apiFetch('/buku');
        const data = await res.json();
        const namaKategori = petaNama(daftarKategori);
        const namaPenulis = petaNama(daftarPenulis);

        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Belum ada buku</td></tr>';
            return;
        }

        tbody.innerHTML = data.map(b => `
            <tr>
                <td><img class="dlms-cover-thumb" src="/uploads/covers/${encodeURIComponent(b.cover)}" onerror="this.style.visibility='hidden'"></td>
                <td>${escapeHtml(b.judul)}</td>
                <td>${escapeHtml(namaKategori[b.kategori_id] ?? '-')}</td>
                <td>${escapeHtml(namaPenulis[b.penulis_id] ?? '-')}</td>
                <td>${escapeHtml(b.tahun_terbit ?? '-')}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick='bukaForm(${JSON.stringify(b)})'>Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="hapusBuku(${b.id})">Hapus</button>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        showAlert('alert-box', 'Gagal memuat data buku.');
    }
}

function bukaForm(buku = null) {
    document.getElementById('form-buku').reset();
    document.getElementById('modal-alert-box').innerHTML = '';
    document.getElementById('cover-existing').textContent = '';
    document.getElementById('pdf-existing').textContent = '';

    if (buku) {
        document.getElementById('modalBukuTitle').textContent = 'Edit Buku';
        document.getElementById('buku-id').value = buku.id;
        document.getElementById('buku-judul').value = buku.judul;
        document.getElementById('buku-deskripsi').value = buku.deskripsi ?? '';
        document.getElementById('buku-kategori').value = buku.kategori_id;
        document.getElementById('buku-penulis').value = buku.penulis_id;
        document.getElementById('buku-tahun').value = buku.tahun_terbit ?? '';
        document.getElementById('cover-existing').textContent = 'File saat ini: ' + buku.cover;
        document.getElementById('pdf-existing').textContent = 'File saat ini: ' + buku.file_pdf;
        // Saat edit, file cover/PDF baru bersifat opsional (lihat perbaikan BukuController::update).
        document.getElementById('cover-required').style.display = 'none';
        document.getElementById('pdf-required').style.display = 'none';
    } else {
        document.getElementById('modalBukuTitle').textContent = 'Tambah Buku';
        document.getElementById('buku-id').value = '';
        document.getElementById('cover-required').style.display = '';
        document.getElementById('pdf-required').style.display = '';
    }
    modalBuku().show();
}

document.getElementById('form-buku').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('buku-id').value;

    const formData = new FormData();
    formData.append('judul', document.getElementById('buku-judul').value);
    formData.append('deskripsi', document.getElementById('buku-deskripsi').value);
    formData.append('kategori_id', document.getElementById('buku-kategori').value);
    formData.append('penulis_id', document.getElementById('buku-penulis').value);
    formData.append('tahun_terbit', document.getElementById('buku-tahun').value);

    const coverFile = document.getElementById('buku-cover').files[0];
    const pdfFile = document.getElementById('buku-pdf').files[0];
    if (coverFile) formData.append('cover', coverFile);
    if (pdfFile) formData.append('file_pdf', pdfFile);

    if (!id && !coverFile) {
        showAlert('modal-alert-box', 'File cover wajib diunggah untuk buku baru.');
        return;
    }
    if (!id && !pdfFile) {
        showAlert('modal-alert-box', 'File PDF wajib diunggah untuk buku baru.');
        return;
    }

    try {
        // Endpoint update buku sengaja tetap pakai method POST (bukan PUT), karena
        // PHP tidak mem-parsing body multipart/form-data pada request PUT tanpa
        // penanganan khusus. Ini konsisten dengan definisi rute di Routes.php.
        const res = await apiFetch(id ? `/buku/${id}` : '/buku', {
            method: 'POST',
            body: formData,
        });

        if (!res.ok) {
            showAlert('modal-alert-box', escapeHtml(await apiErrorMessage(res)));
            return;
        }

        modalBuku().hide();
        showAlert('alert-box', 'Buku berhasil disimpan.', 'success');
        muatBuku();
    } catch (e) {
        showAlert('modal-alert-box', 'Gagal menyimpan buku.');
    }
});

async function hapusBuku(id) {
    if (!confirm('Yakin ingin menghapus buku ini? File cover & PDF terkait juga akan dihapus.')) return;

    try {
        const res = await apiFetch(`/buku/${id}`, { method: 'DELETE' });
        if (!res.ok) {
            showAlert('alert-box', escapeHtml(await apiErrorMessage(res)));
            return;
        }
        showAlert('alert-box', 'Buku berhasil dihapus.', 'success');
        muatBuku();
    } catch (e) {
        showAlert('alert-box', 'Gagal menghapus buku.');
    }
}

if (dlmsRequireAdmin()) {
    muatOpsi().then(muatBuku);
}
</script>
</body>
</html>
