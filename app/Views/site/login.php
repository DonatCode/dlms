<!DOCTYPE html>
<html lang="id">
<head>
<?= view('site/partials/head', ['title' => 'Masuk']) ?>
</head>
<body class="min-h-screen flex flex-col">

<header class="fixed top-0 left-0 w-full z-50 h-[72px] flex items-center px-margin-mobile md:px-margin-desktop bg-surface-container-lowest border-b border-outline-variant">
    <a href="/" class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-[28px]">menu_book</span>
        <span class="font-headline-md text-headline-md font-bold text-primary">BaBook</span>
    </a>
</header>

<main class="flex-grow flex items-center justify-center pt-[72px] px-margin-mobile">
    <div class="w-full max-w-[440px]">
        <div class="bg-surface-container-lowest rounded-xl p-8 md:p-12 border border-outline-variant custom-shadow">
            <div class="text-center mb-stack-lg">
                <h1 class="font-headline-lg text-headline-lg text-on-surface mb-2">Masuk ke Akun Anda</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">Selamat datang kembali! Silakan masuk untuk mengunduh buku.</p>
            </div>

            <div id="alert-box"></div>

            <form id="form-login" class="space-y-stack-md">
                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block" for="email">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]">mail</span>
                        <input class="w-full pl-12 pr-4 py-3 border border-outline-variant rounded-lg outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" id="email" type="email" placeholder="nama@contoh.com" required autofocus>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="font-label-md text-label-md text-on-surface-variant block" for="password">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]">lock</span>
                        <input class="w-full pl-12 pr-4 py-3 border border-outline-variant rounded-lg outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" id="password" type="password" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" id="btn-login" class="w-full py-4 bg-primary text-on-primary font-label-md text-label-md rounded-lg shadow-sm hover:opacity-90 transition-all active:scale-[0.98]">
                    Masuk
                </button>
            </form>

            <div class="mt-stack-lg text-center">
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Belum punya akun? <a href="/register" class="font-label-md text-label-md text-primary font-bold hover:underline">Daftar di sini</a>
                </p>
            </div>
        </div>
    </div>
</main>

<script src="/assets/site/site.js"></script>
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
            showAlert('alert-box', escapeHtml(body.message || 'Email atau password salah'));
            return;
        }

        dlmsSaveSession(body.token, body.role, '');

        // api/login tidak mengembalikan nama, jadi diambil dari api/profile.
        try {
            const profileRes = await apiFetch('/profile');
            if (profileRes.ok) {
                const profile = await profileRes.json();
                dlmsSaveSession(body.token, body.role, profile.nama);
            }
        } catch (e) { /* nama tidak wajib, lanjut saja */ }

        const params = new URLSearchParams(window.location.search);
        window.location.href = params.get('redirect') || '/dashboard';
    } catch (err) {
        showAlert('alert-box', 'Tidak bisa menghubungi server.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Masuk';
    }
});
</script>
</body>
</html>
