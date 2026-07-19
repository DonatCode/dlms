<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Penulis - DLMS Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/admin/admin.css" rel="stylesheet">
</head>
<body>

<?= view('admin/partials/nav', ['active' => $active]) ?>

<main class="dlms-content">
    <div class="dlms-topbar">
        <h4 class="mb-0">Penulis</h4>
        <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#modalPenulis" onclick="bukaForm()">
            + Tambah Penulis
        </button>
    </div>

    <div id="alert-box"></div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table dlms-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:60px">ID</th>
                        <th>Nama Penulis</th>
                        <th style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel-penulis">
                    <tr><td colspan="3" class="text-center text-muted py-4">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal tambah/edit penulis -->
<div class="modal fade" id="modalPenulis" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-penulis">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPenulisTitle">Tambah Penulis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-alert-box"></div>
                    <input type="hidden" id="penulis-id">
                    <div class="mb-3">
                        <label class="form-label">Nama Penulis</label>
                        <input type="text" class="form-control" id="penulis-nama" required>
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
const modalPenulis = () => new bootstrap.Modal(document.getElementById('modalPenulis'));

async function muatPenulis() {
    const tbody = document.getElementById('tabel-penulis');
    try {
        const res = await apiFetch('/penulis');
        const data = await res.json();

        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Belum ada penulis</td></tr>';
            return;
        }

        tbody.innerHTML = data.map(p => `
            <tr>
                <td>${p.id}</td>
                <td>${escapeHtml(p.nama)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick='bukaForm(${JSON.stringify(p)})'>Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="hapusPenulis(${p.id})">Hapus</button>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        showAlert('alert-box', 'Gagal memuat data penulis.');
    }
}

function bukaForm(penulis = null) {
    document.getElementById('form-penulis').reset();
    document.getElementById('modal-alert-box').innerHTML = '';
    if (penulis) {
        document.getElementById('modalPenulisTitle').textContent = 'Edit Penulis';
        document.getElementById('penulis-id').value = penulis.id;
        document.getElementById('penulis-nama').value = penulis.nama;
    } else {
        document.getElementById('modalPenulisTitle').textContent = 'Tambah Penulis';
        document.getElementById('penulis-id').value = '';
    }
    modalPenulis().show();
}

document.getElementById('form-penulis').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('penulis-id').value;
    const nama = document.getElementById('penulis-nama').value;

    try {
        const res = await apiFetch(id ? `/penulis/${id}` : '/penulis', {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nama }),
        });

        if (!res.ok) {
            showAlert('modal-alert-box', escapeHtml(await apiErrorMessage(res)));
            return;
        }

        modalPenulis().hide();
        showAlert('alert-box', 'Penulis berhasil disimpan.', 'success');
        muatPenulis();
    } catch (e) {
        showAlert('modal-alert-box', 'Gagal menyimpan penulis.');
    }
});

async function hapusPenulis(id) {
    if (!confirm('Yakin ingin menghapus penulis ini?')) return;

    try {
        const res = await apiFetch(`/penulis/${id}`, { method: 'DELETE' });
        if (!res.ok) {
            showAlert('alert-box', escapeHtml(await apiErrorMessage(res)));
            return;
        }
        showAlert('alert-box', 'Penulis berhasil dihapus.', 'success');
        muatPenulis();
    } catch (e) {
        showAlert('alert-box', 'Gagal menghapus penulis.');
    }
}

if (dlmsRequireAdmin()) {
    muatPenulis();
}
</script>
</body>
</html>
