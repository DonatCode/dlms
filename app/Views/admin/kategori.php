<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kategori - DLMS Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/admin/admin.css" rel="stylesheet">
</head>
<body>

<?= view('admin/partials/nav', ['active' => $active]) ?>

<main class="dlms-content">
    <div class="dlms-topbar">
        <h4 class="mb-0">Kategori</h4>
        <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#modalKategori" onclick="bukaForm()">
            + Tambah Kategori
        </button>
    </div>

    <div id="alert-box"></div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table dlms-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:60px">ID</th>
                        <th>Nama Kategori</th>
                        <th style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel-kategori">
                    <tr><td colspan="3" class="text-center text-muted py-4">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal tambah/edit kategori -->
<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-kategori">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKategoriTitle">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-alert-box"></div>
                    <input type="hidden" id="kategori-id">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="kategori-nama" required>
                    </div>
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
const modalKategori = () => new bootstrap.Modal(document.getElementById('modalKategori'));

async function muatKategori() {
    const tbody = document.getElementById('tabel-kategori');
    try {
        const res = await apiFetch('/kategori');
        const data = await res.json();

        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Belum ada kategori</td></tr>';
            return;
        }

        tbody.innerHTML = data.map(k => `
            <tr>
                <td>${k.id}</td>
                <td>${escapeHtml(k.nama)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick='bukaForm(${JSON.stringify(k)})'>Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="hapusKategori(${k.id})">Hapus</button>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        showAlert('alert-box', 'Gagal memuat data kategori.');
    }
}

function bukaForm(kategori = null) {
    document.getElementById('form-kategori').reset();
    document.getElementById('modal-alert-box').innerHTML = '';
    if (kategori) {
        document.getElementById('modalKategoriTitle').textContent = 'Edit Kategori';
        document.getElementById('kategori-id').value = kategori.id;
        document.getElementById('kategori-nama').value = kategori.nama;
    } else {
        document.getElementById('modalKategoriTitle').textContent = 'Tambah Kategori';
        document.getElementById('kategori-id').value = '';
    }
    modalKategori().show();
}

document.getElementById('form-kategori').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('kategori-id').value;
    const nama = document.getElementById('kategori-nama').value;

    try {
        const res = await apiFetch(id ? `/kategori/${id}` : '/kategori', {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nama }),
        });

        if (!res.ok) {
            showAlert('modal-alert-box', escapeHtml(await apiErrorMessage(res)));
            return;
        }

        modalKategori().hide();
        showAlert('alert-box', 'Kategori berhasil disimpan.', 'success');
        muatKategori();
    } catch (e) {
        showAlert('modal-alert-box', 'Gagal menyimpan kategori.');
    }
});

async function hapusKategori(id) {
    if (!confirm('Yakin ingin menghapus kategori ini?')) return;

    try {
        const res = await apiFetch(`/kategori/${id}`, { method: 'DELETE' });
        if (!res.ok) {
            // Contoh: kategori masih dipakai buku -> ditolak server dengan status 409
            showAlert('alert-box', escapeHtml(await apiErrorMessage(res)));
            return;
        }
        showAlert('alert-box', 'Kategori berhasil dihapus.', 'success');
        muatKategori();
    } catch (e) {
        showAlert('alert-box', 'Gagal menghapus kategori.');
    }
}

if (dlmsRequireAdmin()) {
    muatKategori();
}
</script>
</body>
</html>
