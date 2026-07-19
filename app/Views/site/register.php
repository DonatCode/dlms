<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Daftar']) ?>
</head>
<body class="min-h-screen flex flex-col">

<header class="fixed top-0 left-0 w-full z-50 h-[72px] flex items-center px-margin-mobile md:px-margin-desktop bg-surface-container-lowest border-b border-outline-variant">
    <a href="/" class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-[28px]">menu_book</span>
        <span class="font-headline-md text-headline-md font-bold text-primary">Perpustakaan online BaBook</span>
    </a>
</header>

<main class="flex-grow flex items-center justify-center pt-[72px] px-margin-mobile py-stack-lg">
    <div class="w-full max-w-[480px] bg-white p-8 md:p-10 rounded-xl border border-outline-variant custom-shadow">
        <div class="text-center mb-stack-md">
            <h1 class="font-headline-lg text-headline-lg text-on-surface mb-2">Buat Akun Baru</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Daftar untuk mulai mengunduh ribuan buku gratis.</p>
        </div>

        <div id="alert-box"></div>

        <form id="form-register" class="flex flex-col gap-5">
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-label-md text-on-surface" for="nama">Nama Lengkap</label>
                <input class="w-full px-4 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" id="nama" placeholder="Nama lengkap" type="text" required>
            </div>
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-label-md text-on-surface" for="email">Email</label>
                <input class="w-full px-4 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" id="email" placeholder="email@contoh.com" type="email" required>
            </div>
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-label-md text-on-surface" for="password">Password</label>
                <input class="w-full px-4 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" id="password" placeholder="Minimal 6 karakter" type="password" required minlength="6">
            </div>
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-label-md text-on-surface" for="confirm_password">Konfirmasi Password</label>
                <input class="w-full px-4 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" id="confirm_password" placeholder="Ulangi password" type="password" required>
            </div>
            <button type="submit" id="btn-register" class="w-full py-4 mt-2 bg-primary text-on-primary rounded-lg font-label-md text-label-md hover:opacity-90 active:scale-[0.98] transition-all flex justify-center items-center gap-2">
                Daftar <span class="material-symbols-outlined">arrow_forward</span>
            </button>
        </form>

        <div class="mt-stack-md pt-stack-md border-t border-outline-variant text-center">
            <p class="font-body-sm text-body-sm text-on-surface-variant">
                Sudah punya akun? <a href="/login" class="text-primary font-bold hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</main>

<script src="/assets/site/site.js"></script>
<script>
document.getElementById('form-register').addEventListener('submit', async (e) => {
    e.preventDefault();

    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    if (password !== confirm) {
        showAlert('alert-box', 'Konfirmasi password tidak cocok.');
        return;
    }

    const btn = document.getElementById('btn-register');
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    try {
        const res = await fetch(API_BASE + '/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                nama: document.getElementById('nama').value,
                email: document.getElementById('email').value,
                password,
            }),
        });
        const body = await res.json();

        if (!res.ok) {
            const pesan = Array.isArray(body?.messages) ? body.messages.join(', ')
                : (typeof body?.messages === 'object' && body?.messages !== null ? Object.values(body.messages).join(', ') : (body?.message || 'Registrasi gagal'));
            showAlert('alert-box', escapeHtml(pesan));
            return;
        }

        showAlert('alert-box', 'Akun berhasil dibuat! Mengarahkan ke halaman login...', 'success');
        setTimeout(() => window.location.href = '/login', 1200);
    } catch (err) {
        showAlert('alert-box', 'Tidak bisa menghubungi server.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Daftar <span class="material-symbols-outlined">arrow_forward</span>';
    }
});
</script>
</body>
</html>
