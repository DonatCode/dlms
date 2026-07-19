# DLMS - Digital Library Management System

Aplikasi CodeIgniter 4 dengan REST API (JWT) untuk mengelola data perpustakaan
digital: **kategori**, **penulis**, dan **buku** (termasuk upload cover & file PDF).
Dokumen ini menjelaskan cara menjalankan proyek di Laragon, bug pada REST API yang
diperbaiki, serta panel admin CRUD yang baru ditambahkan.

---

## 1. Setup lokal (Laragon + MySQL)

### 1.1. Virtual host harus mengarah ke folder `public/`

Ini penyebab paling umum error "404" atau CSS/JS admin tidak kebaca di Laragon:
Laragon secara default membuat auto virtual host yang mengarah ke **root folder
proyek** (`C:\laragon\www\dlms`), padahal CI4 harus diakses lewat folder `public/`.

Cara memperbaiki:
1. Klik kanan Laragon tray icon → **Apache** → **sites-enabled** → cari file
   `auto.dlms.test.conf` (atau nama proyekmu), lalu ubah `DocumentRoot` dan
   `<Directory>` supaya menunjuk ke `.../dlms/public`, bukan `.../dlms`.
2. Atau lebih mudah: matikan auto vhost untuk proyek ini dan buat vhost manual
   yang document root-nya `public/`.
3. Restart Apache dari Laragon.

Setelah ini, semua path yang dipakai di kode (`/api/...`, `/admin/...`,
`/uploads/...`) akan bekerja tanpa perlu embel-embel `index.php` di URL.

### 1.2. Konfigurasi `.env`

File `env` bawaan CI4 tidak ikut ter-upload/commit (memang sengaja di-gitignore).
Salin `env` menjadi `.env` di root proyek, lalu isi minimal:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://dlms.test/'

database.default.hostname = localhost
database.default.database = dlms
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi

JWT_SECRET = 'ganti-dengan-string-acak-yang-panjang'
```

`JWT_SECRET` **wajib diisi**. Sebelumnya kalau variabel ini kosong, filter JWT bisa
fatal error (lihat bagian bug #4 di bawah) — sekarang sudah dibuatkan pesan error
yang jelas, tapi tetap harus diisi supaya login/JWT berfungsi.

### 1.3. Install dependency & migrasi

```bash
composer install
php spark migrate
php spark db:seed DatabaseSeeder
```

Seeder `UserSeeder` akan membuat 2 akun contoh:

| Role  | Email                      | Password  |
|-------|-----------------------------|-----------|
| admin | admin@perpustakaan.com      | admin123  |
| user  | kevin@gmail.com             | 12345678  |

Gunakan akun **admin** untuk login ke panel admin (`/admin/login`).

---

## 2. Bug pada REST API yang diperbaiki

Semua perbaikan sudah diberi komentar inline di kode dengan penjelasan
"sebelumnya begini → sekarang begini". Ringkasannya:

### 2.1. Endpoint tulis kategori/penulis/buku bisa diakses oleh **user biasa**, bukan cuma admin
**File:** `app/Config/Routes.php`
Filter `admin` sudah didefinisikan di `app/Filters/AdminFilter.php` dan sudah
terdaftar sebagai alias di `app/Config/Filters.php`, **tapi tidak pernah dipasang
ke rute manapun**. Rute create/update/delete untuk kategori, penulis, dan buku
hanya dipasangi filter `jwt`, artinya siapa pun yang punya token login (termasuk
role `user`) bisa menambah/mengubah/menghapus data buku.

**Perbaikan:** filter grup diubah dari `['filter' => 'jwt']` menjadi
`['filter' => ['jwt', 'admin']]` untuk ketiga resource tersebut.

### 2.2. Filter CORS didefinisikan tapi tidak pernah dipakai
**File:** `app/Config/Filters.php`, `app/Config/Cors.php`
Alias `'cors' => Cors::class` sudah ada, tapi tidak pernah dimasukkan ke
`$filters['cors']`, sehingga request `OPTIONS` (preflight) dan header
`Access-Control-Allow-*` tidak pernah dikirim balik oleh server. Konfigurasi
`Config/Cors.php` juga masih kosong semua (`allowedOrigins: []`, dst).

**Perbaikan:**
- `$filters['cors'] = ['before' => ['api/*']]` di `Config/Filters.php`.
- `Config/Cors.php` diisi origin umum untuk dev lokal (`localhost`, port umum,
  serta pola `*.test` bawaan Laragon) dan `allowedHeaders` ditambah
  `Authorization` (karena JWT dikirim lewat header ini).

Ini penting kalau nanti REST API dipakai dari frontend terpisah (SPA/mobile),
bukan cuma dari panel admin yang satu origin dengan server.

### 2.3. `JwtFilter` bisa fatal error, bukan membalas JSON 401
**File:** `app/Filters/JwtFilter.php`
`JWT::decode()` dari library `firebase/php-jwt` bisa melempar `TypeError`
(mis. saat `JWT_SECRET` kosong) atau error lain yang **bukan turunan
`\Exception`**, sementara kode lama hanya `catch (\Exception $e)`. Akibatnya
request dengan token bermasalah bisa membuat aplikasi fatal error alih-alih
membalas JSON yang rapi.

**Perbaikan:** tangkap `\Throwable` (mencakup `Error` dan `Exception`), dan
tambahkan pengecekan eksplisit kalau `JWT_SECRET` belum di-set di `.env` →
dibalas `500` dengan pesan yang jelas, bukan crash.

### 2.4. `BukuController::create()` bisa gagal INSERT karena kolom NOT NULL
**File:** `app/Controllers/BukuController.php`, migrasi `CreateBuku`
Kolom `cover` dan `file_pdf` di migrasi bersifat `NOT NULL`, tapi kode lama
mengizinkan `create()` berjalan meski file cover/PDF tidak diunggah (nilainya
jadi `null`), yang berujung query INSERT gagal di level database dengan pesan
error SQL mentah.

**Perbaikan:** validasi eksplisit — cover dan file PDF wajib diunggah saat
membuat buku baru, dengan pesan error yang jelas sebelum menyentuh database.

### 2.5. `kategori_id` / `penulis_id` tidak divalidasi sebelum insert/update
**File:** `app/Controllers/BukuController.php`
ID kategori/penulis yang tidak ada di database langsung diloloskan ke query,
baru gagal di level constraint foreign key dengan pesan error SQL mentah
(membocorkan detail internal, bukan respons API yang rapi).

**Perbaikan:** dicek dulu dengan `KategoriModel`/`PenulisModel` sebelum insert
atau update; kalau tidak ditemukan, dibalas `400` dengan pesan jelas.

### 2.6. `BukuController::update()` bisa menimpa data lama dengan `null`
**File:** `app/Controllers/BukuController.php`
`$this->request->getPost(['judul', 'deskripsi', ...])` tetap mengembalikan key
dengan nilai `null` untuk field yang **tidak dikirim** oleh form/klien. Nilai
`null` itu ikut di-`update()` ke database, jadi kalau klien lupa mengirim satu
field saja, data lama pada kolom itu bisa hilang tertimpa `NULL`.

**Perbaikan:** `array_filter($data, fn($v) => $v !== null)` untuk membuang key
yang memang tidak dikirim, sehingga update bersifat parsial dan aman.

### 2.7. File lama tidak pernah dihapus saat cover/PDF diganti
**File:** `app/Controllers/BukuController.php`
Saat admin mengganti cover/PDF sebuah buku, file versi lama dibiarkan menumpuk
selamanya di `public/uploads/...` karena tidak pernah dihapus.

**Perbaikan:** file lama otomatis dihapus (`unlink`) begitu file pengganti
berhasil diunggah.

### 2.8. Menghapus kategori/penulis diam-diam menghapus semua bukunya
**File:** `app/Controllers/KategoriController.php`, `PenulisController.php`,
migrasi `CreateBuku`
Foreign key `buku.kategori_id` dan `buku.penulis_id` didefinisikan dengan
`ON DELETE CASCADE`. Artinya menghapus 1 kategori/penulis akan **menghapus
semua buku terkait secara diam-diam** tanpa peringatan — sangat berisiko untuk
tombol "Hapus" di panel admin.

**Perbaikan:** endpoint `delete()` kategori/penulis sekarang mengecek dulu
apakah masih ada buku yang memakainya; kalau ada, ditolak dengan status `409`
dan pesan jumlah buku yang masih terkait, supaya admin sadar dan memindahkan
buku itu dulu.

### 2.9. Body JSON kosong/invalid memicu warning PHP 8
**File:** `app/Controllers/KategoriController.php`, `PenulisController.php`
`$this->request->getJSON(true)` mengembalikan `null` kalau body request kosong
atau bukan JSON valid. Kode lama langsung mengakses `$data['nama']` tanpa
pengecekan, yang memicu warning "Trying to access array offset on value of
type null" di PHP 8.

**Perbaikan:** `$this->request->getJSON(true) ?? []` sebagai penjaga, plus
validasi `nama` wajib diisi juga ditambahkan ke `update()` (sebelumnya hanya
ada di `create()`).

### 2.10. Registrasi tidak validasi format email & panjang password
**File:** `app/Controllers/AuthController.php`
`register()` cuma mengecek field tidak kosong, tanpa validasi format email
atau panjang minimal password.

**Perbaikan:** ditambah `filter_var(..., FILTER_VALIDATE_EMAIL)` dan minimal
6 karakter untuk password.

> Catatan: `role` pada `register()` **selalu** di-hardcode `'user'` di kode
> lama maupun baru — jadi tidak ada celah privilege escalation lewat endpoint
> registrasi ini. Ini sudah aman, tidak diubah.

### 2.11. Folder aset `public/admin/` bentrok dengan rute `/admin`
**File:** lokasi aset CSS/JS panel admin
Awalnya aset ditaruh di `public/admin/admin.css` dan `public/admin/admin.js`.
Karena `public/admin/` adalah **folder sungguhan** di disk, Apache menganggap
request ke `/admin` (dashboard) sebagai permintaan ke folder itu, bukan
diteruskan ke CI4 lewat `.htaccess` — dan karena *directory listing* dimatikan,
hasilnya `403 Forbidden` setelah login.

**Perbaikan:** aset dipindah ke `public/assets/admin/` supaya tidak akan
pernah bentrok dengan nama rute apa pun.

### 2.12. Folder `public/uploads/pdf/` tidak ada
**File:** struktur folder `public/uploads/`
Hanya `public/uploads/covers/` yang tersedia; folder `public/uploads/pdf/`
tempat file PDF buku disimpan tidak ada sama sekali. `move()` di CI4 **tidak**
otomatis membuat folder tujuan kalau belum ada, jadi setiap kali admin
mengunggah PDF buku baru, proses upload akan gagal secara diam-diam/error.

**Perbaikan:** folder `public/uploads/pdf/` dibuat (dengan `index.html` kosong
di dalamnya, mengikuti pola folder `covers/`, supaya isi folder tidak bisa
dilist langsung lewat browser).

---

## 3. Panel Admin (baru)

### 3.1. Alur & desain

Panel admin **tidak menambah sistem login baru** — dia memakai REST API yang
sudah ada (`api/login`, `api/kategori`, `api/penulis`, `api/buku`) lewat
JavaScript di browser:

1. Admin login di `/admin/login` → form mengirim `POST /api/login`.
2. Kalau `role` yang dikembalikan bukan `admin`, panel menolak masuk walau
   email/password benar (karena `api/login` memang berlaku untuk semua role).
3. Token JWT disimpan di `localStorage`, lalu dipakai sebagai header
   `Authorization: Bearer <token>` di setiap request berikutnya.
4. Semua halaman admin (`/admin`, `/admin/kategori`, `/admin/penulis`,
   `/admin/buku`) memanggil endpoint REST yang sama untuk list/create/update/
   delete data.

**Dua lapis proteksi:**
- **Client-side** (`public/assets/admin/admin.js` → `dlmsRequireAdmin()`): kalau tidak
  ada token atau role bukan admin, langsung redirect ke halaman login. Ini
  hanya untuk kenyamanan tampilan.
- **Server-side** (yang sebenarnya menegakkan keamanan): filter `['jwt',
  'admin']` di `Config/Routes.php` pada setiap endpoint tulis. Jadi walau
  seseorang membuka HTML `/admin/buku` langsung tanpa lewat login, dia tetap
  tidak bisa mengubah data apa pun tanpa token admin yang valid — request-nya
  akan dibalas `401`/`403` oleh API.

### 3.2. Struktur file yang ditambahkan

```
app/Controllers/AdminController.php     # menyajikan halaman (view saja)
app/Views/admin/login.php
app/Views/admin/dashboard.php
app/Views/admin/kategori.php
app/Views/admin/penulis.php
app/Views/admin/buku.php
app/Views/admin/partials/nav.php        # sidebar, dipakai ulang di semua halaman
public/assets/admin/admin.css            # styling bersama
public/assets/admin/admin.js             # helper auth + pemanggil REST API (apiFetch dst)
```

### 3.3. Rute halaman

| Rute               | Fungsi                                   |
|---------------------|-------------------------------------------|
| `/admin/login`      | Form login admin                          |
| `/admin`             | Dashboard (ringkasan jumlah data)         |
| `/admin/kategori`   | CRUD kategori                             |
| `/admin/penulis`    | CRUD penulis                              |
| `/admin/buku`       | CRUD buku (dengan upload cover & PDF)     |

### 3.4. Hal-hal yang perlu diperhatikan saat pakai

- Saat **menambah** buku baru, file **cover dan PDF wajib diisi** (lihat bug
  2.4 di atas — kolomnya `NOT NULL` di database).
- Saat **mengedit** buku, cover/PDF bersifat opsional — kosongkan saja kalau
  tidak ingin menggantinya, data lama tetap dipakai.
- Menghapus kategori/penulis yang masih dipakai buku akan ditolak (bug 2.8) —
  pindahkan dulu buku-buku terkait ke kategori/penulis lain, atau hapus
  bukunya lebih dulu.
- Update buku (`api/buku/{id}`) sengaja tetap memakai method **POST**, bukan
  PUT — ini bukan bug, tapi keterbatasan PHP yang tidak mem-parsing body
  `multipart/form-data` pada request PUT tanpa penanganan khusus. Endpoint
  kategori/penulis (tanpa upload file) tetap pakai PUT seperti REST API pada
  umumnya.

---

## 4. Tampilan User (Mode Tanpa Login & Mode Login)

Situs publik dibangun mengadaptasi desain dari
`stitch_design_to_web_implementation/` (12 halaman Stitch) ke dalam view CI4,
dengan data asli dari REST API yang sudah ada — bukan lagi data contoh statis.

### 4.1. Dua mode akses

| | **Tanpa login (tamu)** | **Sudah login** |
|---|---|---|
| Lihat Beranda, Koleksi, Detail Buku, Kategori, Penulis | ✅ | ✅ |
| Cari & filter buku (judul, kategori, penulis) | ✅ | ✅ |
| **Baca buku online** (`/buku/{id}/baca`) | ✅ | ✅ |
| **Unduh file PDF** | ❌ → diarahkan ke `/login` | ✅ |
| Bookmark buku | ❌ → diarahkan ke `/login` | ✅ |
| Dashboard, Buku Saya, Riwayat Unduh | ❌ | ✅ |

Ini sesuai definisi yang diminta: tanpa login **hanya bisa membaca** di situs
(browsing + baca online), sedangkan **mengunduh** file PDF wajib login.

Desain sengaja membedakan dua hal ini:
- **"Baca Sekarang"** di halaman detail buku membuka `/buku/{id}/baca`, sebuah
  pembaca PDF sederhana (`<iframe>`) yang memuat file langsung dari
  `public/uploads/pdf/` — publik, tidak butuh token, karena tamu memang boleh
  membaca.
- **"Unduh PDF"** memanggil `GET api/buku/{id}/download` yang **wajib token
  JWT** (filter `jwt` di `Routes.php`). Setiap unduhan berhasil dicatat ke
  tabel `pengunduhan` (dipakai untuk halaman "Riwayat Unduh" dan "Buku Saya").

> Catatan jujur soal batasannya: karena file PDF tetap ada di `public/uploads/pdf/`
> (supaya bisa dibaca tamu tanpa login), secara teknis siapa pun yang tahu
> nama file acaknya *bisa* saja mengaksesnya langsung lewat URL tanpa lewat
> tombol "Unduh". Ini bukan celah baru — konsekuensi wajar dari syarat "tamu
> boleh membaca isi buku". Tombol "Unduh" tetap berguna untuk pengalaman resmi
> (nama file rapi, tercatat di riwayat), bukan sebagai DRM/proteksi konten.

### 4.2. Struktur file yang ditambahkan

```
app/Controllers/SiteController.php         # halaman publik (beranda, koleksi, detail, kategori, penulis, login, register, baca)
app/Controllers/AccountController.php      # halaman akun (dashboard, buku saya, bookmark, riwayat unduh)
app/Controllers/BookmarkController.php     # API bookmark (jwt)
app/Controllers/PengunduhanController.php  # API riwayat unduh & buku saya (jwt)
app/Models/BookmarkModel.php
app/Models/PengunduhanModel.php

app/Views/site/beranda.php
app/Views/site/koleksi.php
app/Views/site/detail-buku.php
app/Views/site/kategori.php
app/Views/site/penulis.php
app/Views/site/login.php
app/Views/site/register.php
app/Views/site/baca-buku.php
app/Views/site/partials/head.php           # <head> + design tokens (Tailwind CDN, sesuai DESIGN.md)
app/Views/site/partials/navbar.php         # navbar dinamis (guest vs login, lihat site.js)
app/Views/site/partials/footer.php
app/Views/site/partials/sidebar-account.php

app/Views/account/dashboard.php
app/Views/account/buku-saya.php
app/Views/account/bookmark.php
app/Views/account/riwayat-unduh.php

public/assets/site/site.css
public/assets/site/site.js                 # auth guard, apiFetch, downloadBuku(), toggleBookmark(), dst
```

Tabel `pengunduhan` dan `bookmark` **sudah ada** dari migrasi awal proyek
(`CreatePengunduhan`, `CreateBookmark`) — tidak perlu migrasi baru. Seeder
`BookmarkSeeder` dan `PengunduhanSeeder` juga sudah tersedia untuk data contoh.

### 4.3. Rute baru

**Publik (tanpa login):**

| Rute | Fungsi |
|---|---|
| `/` | Beranda |
| `/koleksi` | Daftar & pencarian buku (`?search=`, `?kategori_id=`, `?penulis_id=`) |
| `/buku/{id}` | Detail buku |
| `/buku/{id}/baca` | Baca buku online (iframe PDF) |
| `/kategori` | Daftar kategori |
| `/penulis` | Daftar penulis |
| `/login` | Login (akun admin maupun user bisa masuk lewat sini) |
| `/register` | Registrasi akun baru (selalu jadi role `user`) |

**Perlu login (redirect ke `/login?redirect=...` kalau belum login):**

| Rute | Fungsi |
|---|---|
| `/dashboard` | Ringkasan aktivitas akun |
| `/buku-saya` | Buku yang pernah diunduh |
| `/bookmark` | Buku yang di-bookmark |
| `/riwayat-unduh` | Log lengkap setiap unduhan |

**API baru:**

| Endpoint | Method | Filter | Fungsi |
|---|---|---|---|
| `api/buku/{id}/download` | GET | `jwt` | Unduh file PDF + catat ke `pengunduhan` |
| `api/bookmark` | GET | `jwt` | Daftar buku yang di-bookmark user login |
| `api/bookmark` | POST | `jwt` | Tambah bookmark, body `{ "buku_id": 1 }` |
| `api/bookmark/{buku_id}` | DELETE | `jwt` | Hapus bookmark |
| `api/riwayat-unduh` | GET | `jwt` | Log unduhan milik user login |
| `api/buku-saya` | GET | `jwt` | Buku unik yang pernah diunduh user login |

Endpoint `api/buku`, `api/kategori`, `api/penulis` (GET) tetap publik seperti
sebelumnya — jadi semua halaman baca-saja di atas bisa jalan tanpa token.

### 4.4. Bagaimana satu sistem login dipakai bersama (admin & user)

Panel admin dan situs user **memakai token JWT yang sama** (kunci
`localStorage` yang sama: `dlms_token`, `dlms_role`, `dlms_nama`), karena
`api/login` memang berlaku untuk semua role. Bedanya cuma pengecekan di sisi
client:
- `admin.js` → `dlmsRequireAdmin()`: tolak kalau role bukan `admin`.
- `site.js` → `siteRequireLogin()`: terima role apa saja, yang penting ada token.

Jadi kalau admin login lewat `/login` (bukan `/admin/login`), dia tetap bisa
memakai fitur user biasa (unduh, bookmark, dst) — cukup masuk akal karena
admin juga seorang "pengguna".

### 4.5. Langkah-langkah menjalankan & menguji (berurutan)

1. **Pastikan bagian Setup di atas (bagian 1) sudah dilakukan**: vhost
   Laragon mengarah ke `public/`, `.env` terisi (`JWT_SECRET`, kredensial DB).

2. **Jalankan migrasi** (kalau proyek masih baru / database kosong):
   ```bash
   php spark migrate
   ```
   Ini akan membuat semua tabel termasuk `pengunduhan` dan `bookmark` yang
   dipakai fitur baru ini.

3. **Jalankan seeder** supaya ada data contoh untuk dicoba:
   ```bash
   php spark db:seed DatabaseSeeder
   ```
   Ini otomatis memanggil `UserSeeder`, `KategoriSeeder`, `PenulisSeeder`,
   `BukuSeeder`, `PengunduhanSeeder`, `BookmarkSeeder` secara berurutan.

   > **Catatan:** `BukuSeeder` memakai nama file contoh (`php.jpg`, `php.pdf`,
   > dst) yang **tidak benar-benar ada** di `public/uploads/`, jadi cover buku
   > hasil seeder akan tampak rusak/kosong di halaman. Ini bawaan data contoh
   > proyek, bukan bug baru. Untuk melihat tampilan yang benar-benar rapi,
   > tambahkan/ganti buku lewat **panel admin** (`/admin/buku`) supaya file
   > cover & PDF-nya asli ter-upload.

4. **Pastikan folder upload ada & bisa ditulis** (harusnya sudah otomatis dari
   perbaikan 2.12): `public/uploads/covers/` dan `public/uploads/pdf/`.

5. **Uji mode tanpa login:**
   - Buka `http://dlms.test/` → harus muncul halaman Beranda (bukan welcome
     page CI4 lagi), lengkap dengan kategori populer & buku terbaru.
   - Klik salah satu buku → halaman Detail Buku muncul.
   - Klik **"Baca Sekarang"** → PDF harus terbuka di `/buku/{id}/baca` tanpa
     diminta login.
   - Klik **"Unduh PDF"** → harus otomatis diarahkan ke `/login` (karena belum
     login).
   - Coba `/koleksi`, `/kategori`, `/penulis` → semua harus bisa diakses dan
     menampilkan data asli dari database.
   - Coba akses langsung `/dashboard` atau `/bookmark` tanpa login → harus
     otomatis dilempar ke `/login`.

6. **Uji registrasi & login:**
   - Buka `/register`, buat akun baru (role otomatis `user`).
   - Login di `/login` dengan akun baru itu, **atau** pakai akun contoh dari
     seeder: `kevin@gmail.com` / `12345678`.
   - Setelah login, navbar di kanan atas harus berubah jadi "Halo, <nama>"
     dengan menu dropdown (Dashboard, Buku Saya, Bookmark, Riwayat Unduh, Keluar).

7. **Uji unduh, bookmark, dan riwayat (dalam kondisi sudah login):**
   - Buka detail salah satu buku, klik **"Unduh PDF"** → file harus terunduh
     ke komputer dengan nama file rapi (judul buku, bukan nama acak di server).
   - Klik **"Simpan"** (ikon bookmark) → tombol berubah jadi "Tersimpan".
   - Buka `/bookmark` → buku tadi harus muncul di daftar; coba hapus lewat
     ikon tempat sampah.
   - Buka `/riwayat-unduh` → harus ada baris baru dengan waktu unduhan barusan.
   - Buka `/buku-saya` → buku yang baru diunduh harus muncul di sana.
   - Buka `/dashboard` → tiga kartu statistik (Buku Saya, Bookmark, Total
     Unduhan) harus menunjukkan angka yang sesuai.

8. **Uji logout:**
   - Klik **"Keluar"** di navbar atau sidebar akun → token dihapus dari
     `localStorage` dan diarahkan kembali ke Beranda sebagai tamu.

---

## 5. Yang belum/tidak diubah (di luar cakupan permintaan ini)

- Belum ada mekanisme refresh token / blacklist token saat logout (JWT masih
  stateless sepenuhnya) — `logout()` di `AuthController` hanya membalas pesan
  sukses tanpa efek di server.
- Belum ada pagination di `index()` kategori/penulis/buku; untuk jumlah data
  kecil (skala tugas kuliah) ini belum jadi masalah.
- Belum ada halaman admin untuk mengelola data `users`, `pengunduhan`, atau
  `bookmark` secara langsung (admin hanya kelola kategori/penulis/buku) —
  meski begitu, data pengunduhan/bookmark tetap otomatis tercatat lewat
  aktivitas user di situs.
- Belum ada fitur lupa password, edit profil, atau ganti password di sisi user.
- Fitur "Baca Sekarang" memakai `<iframe>` ke PDF mentah (viewer bawaan
  browser) — belum ada reader kustom dengan daftar isi/bab seperti pada
  mockup desain (`12._baca_buku_viewer`), karena itu perlu parsing struktur
  PDF yang di luar cakupan data yang tersimpan saat ini (hanya 1 file PDF utuh
  per buku, tanpa metadata bab).
