<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - DLMS Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/admin/admin.css" rel="stylesheet">
</head>
<body>

<?= view('admin/partials/nav', ['active' => $active]) ?>

<main class="dlms-content">
    <div class="dlms-topbar">
        <h4 class="mb-0">Dashboard</h4>
    </div>

    <div id="alert-box"></div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card dlms-stat-card">
                <div class="card-body">
                    <div class="text-muted small">Total Kategori</div>
                    <div class="stat-number" id="stat-kategori">-</div>
                    <a href="/admin/kategori" class="small">Kelola kategori &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dlms-stat-card">
                <div class="card-body">
                    <div class="text-muted small">Total Penulis</div>
                    <div class="stat-number" id="stat-penulis">-</div>
                    <a href="/admin/penulis" class="small">Kelola penulis &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dlms-stat-card">
                <div class="card-body">
                    <div class="text-muted small">Total Buku</div>
                    <div class="stat-number" id="stat-buku">-</div>
                    <a href="/admin/buku" class="small">Kelola buku &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="/assets/admin/admin.js"></script>
<script>
if (dlmsRequireAdmin()) {
    (async () => {
        try {
            const [kategoriRes, penulisRes, bukuRes] = await Promise.all([
                apiFetch('/kategori'),
                apiFetch('/penulis'),
                apiFetch('/buku'),
            ]);

            if (kategoriRes.ok) document.getElementById('stat-kategori').textContent = (await kategoriRes.json()).length;
            if (penulisRes.ok) document.getElementById('stat-penulis').textContent = (await penulisRes.json()).length;
            if (bukuRes.ok) document.getElementById('stat-buku').textContent = (await bukuRes.json()).length;
        } catch (e) {
            showAlert('alert-box', 'Gagal memuat data ringkasan. Pastikan server API berjalan.');
        }
    })();
}
</script>
</body>
</html>
