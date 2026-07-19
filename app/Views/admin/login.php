<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin - DLMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/admin/admin.css" rel="stylesheet">
</head>
<body>
<div class="dlms-login-wrap">
    <div class="card dlms-login-card shadow-lg">
        <div class="card-body p-4">
            <h4 class="text-center mb-1">📚 DLMS Admin</h4>
            <p class="text-center text-muted small mb-4">Digital Library Management System</p>

            <div id="alert-box"></div>

            <form id="form-login">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" required>
                </div>
                <button type="submit" class="btn btn-dark w-100" id="btn-login">Masuk</button>
            </form>
        </div>
    </div>
</div>

<script src="/assets/admin/admin.js"></script>
<script>
document.getElementById('form-login').addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn = document.getElementById('btn-login');
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    try {
        const res = await fetch(API_BASE + '/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            }),
        });

        const body = await res.json();

        if (!res.ok) {
            showAlert('alert-box', escapeHtml(body.message || 'Login gagal'));
            return;
        }

        // Endpoint api/login berlaku untuk semua role (admin maupun user biasa),
        // jadi khusus di panel admin kita cek ulang role-nya di sini. Kalau bukan
        // admin, tolak masuk walaupun kombinasi email/password-nya benar.
        if (body.role !== 'admin') {
            showAlert('alert-box', 'Akun ini bukan admin, tidak bisa mengakses panel ini.');
            return;
        }

        dlmsSaveSession(body.token, body.role, '');

        // api/login tidak mengembalikan nama, jadi diambil terpisah dari api/profile.
        try {
            const profileRes = await apiFetch('/profile');
            if (profileRes.ok) {
                const profile = await profileRes.json();
                dlmsSaveSession(body.token, body.role, profile.nama);
            }
        } catch (e) {
            // Tidak fatal kalau gagal ambil nama, lanjut saja ke dashboard.
        }

        window.location.href = '/admin';
    } catch (err) {
        showAlert('alert-box', 'Tidak bisa menghubungi server. Pastikan Laragon & database aktif.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Masuk';
    }
});
</script>
</body>
</html>
