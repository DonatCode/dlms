# DLMS API Contract (Sprint W3)

**Digital Library Management System - Core API Documentation**

- **Status**: Sprint W3 - Complete ✅
- **Framework**: CodeIgniter 4
- **Database**: MySQL
- **Authentication**: JWT (firebase/php-jwt)
- **Password Hashing**: bcrypt

---

## Base URL

```
http://localhost:8080/api
```

## Authentication

Semua endpoint yang diberi label `[AUTH]` memerlukan JWT token di header:

```
Authorization: Bearer <token>
```

Token diperoleh dari endpoint `/login`.

---

## Endpoints Summary

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/register` | ❌ | Registrasi user baru |
| POST | `/login` | ❌ | Login user |
| POST | `/logout` | ✅ | Logout user |
| GET | `/profile` | ✅ | Get profile user |
| GET | `/kategori` | ❌ | Daftar semua kategori |
| GET | `/kategori/:id` | ❌ | Detail kategori |
| POST | `/kategori` | ✅ | Buat kategori baru |
| PUT | `/kategori/:id` | ✅ | Update kategori |
| DELETE | `/kategori/:id` | ✅ | Hapus kategori |
| GET | `/penulis` | ❌ | Daftar semua penulis |
| GET | `/penulis/:id` | ❌ | Detail penulis |
| POST | `/penulis` | ✅ | Buat penulis baru |
| PUT | `/penulis/:id` | ✅ | Update penulis |
| DELETE | `/penulis/:id` | ✅ | Hapus penulis |
| GET | `/buku` | ❌ | Daftar semua buku |
| GET | `/buku/:id` | ❌ | Detail buku |
| POST | `/buku` | ✅ | Buat buku baru (upload cover & PDF) |
| POST | `/buku/:id` | ✅ | Update buku |
| DELETE | `/buku/:id` | ✅ | Hapus buku |

---

## 📝 API Details

### 1. Register

**POST** `/register`

Registrasi user baru sebagai role "user".

**Request Body (application/json):**
```json
{
    "nama": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (201 Created):**
```json
{
    "message": "Akun berhasil dibuat"
}
```

**Error (400 Bad Request):**
```json
{
    "message": "Email sudah terdaftar"
}
```

---

### 2. Login

**POST** `/login`

Login user dengan email & password, mendapatkan JWT token.

**Request Body (application/json):**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200 OK):**
```json
{
    "message": "Login berhasil",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEiLCJyb2xlIjoidXNlciIsImV4cCI6MTc4MzM0MTEzMX0.5xFBJmQ2fR5AiMNwziKewWmtx6ZL7T7_YeWDqGBf9GE",
    "role": "user"
}
```

**Error (401 Unauthorized):**
```json
{
    "message": "Email atau password salah"
}
```

---

### 3. Logout [AUTH]

**POST** `/logout`

Logout user (JWT stateless, token cukup dihapus di client).

**Headers:**
```
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
    "message": "Logout berhasil"
}
```

---

### 4. Profile [AUTH]

**GET** `/profile`

Mendapatkan data profil user yang sedang login.

**Headers:**
```
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
    "id": 1,
    "nama": "John Doe",
    "email": "john@example.com",
    "role": "user"
}
```

**Error (401 Unauthorized):**
```json
{
    "message": "Token tidak ditemukan"
}
```

---

## 📚 Kategori Endpoints

### 5. Get All Kategori

**GET** `/kategori`

Mendapatkan daftar semua kategori.

**Response (200 OK):**
```json
[
    {
        "id": 7,
        "nama": "Fiksi"
    },
    {
        "id": 2,
        "nama": "Teknologi"
    },
    {
        "id": 9,
        "nama": "Non-Fiksi"
    }
]
```

---

### 6. Get Detail Kategori

**GET** `/kategori/:id`

Mendapatkan detail kategori berdasarkan ID.

**Parameters:**
- `id` (path): ID kategori

**Response (200 OK):**
```json
{
    "id": 7,
    "nama": "Fiksi"
}
```

**Error (404 Not Found):**
```json
{
    "message": "Kategori tidak ditemukan"
}
```

---

### 7. Create Kategori [AUTH]

**POST** `/kategori`

Membuat kategori baru (hanya user yang login).

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body (application/json):**
```json
{
    "nama": "Mystery"
}
```

**Response (201 Created):**
```json
{
    "nama": "Mystery"
}
```

---

### 8. Update Kategori [AUTH]

**PUT** `/kategori/:id`

Mengupdate kategori (hanya user yang login).

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Parameters:**
- `id` (path): ID kategori

**Request Body (application/json):**
```json
{
    "nama": "Mystery - Updated"
}
```

**Response (200 OK):**
```json
{
    "nama": "Mystery - Updated"
}
```

---

### 9. Delete Kategori [AUTH]

**DELETE** `/kategori/:id`

Menghapus kategori (hanya user yang login).

**Headers:**
```
Authorization: Bearer <token>
```

**Parameters:**
- `id` (path): ID kategori

**Response (200 OK):**
```json
{
    "id": "7"
}
```

---

## ✍️ Penulis Endpoints

### 10. Get All Penulis

**GET** `/penulis`

Mendapatkan daftar semua penulis.

**Response (200 OK):**
```json
[
    {
        "id": 2,
        "nama": "Andrea Hirata"
    },
    {
        "id": 7,
        "nama": "Tere Liye"
    }
]
```

---

### 11. Get Detail Penulis

**GET** `/penulis/:id`

Mendapatkan detail penulis berdasarkan ID.

**Parameters:**
- `id` (path): ID penulis

**Response (200 OK):**
```json
{
    "id": 2,
    "nama": "Andrea Hirata"
}
```

---

### 12. Create Penulis [AUTH]

**POST** `/penulis`

Membuat penulis baru (hanya user yang login).

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body (application/json):**
```json
{
    "nama": "Dewi Lestari"
}
```

**Response (201 Created):**
```json
{
    "nama": "Dewi Lestari"
}
```

---

### 13. Update Penulis [AUTH]

**PUT** `/penulis/:id`

Mengupdate penulis (hanya user yang login).

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Parameters:**
- `id` (path): ID penulis

**Request Body (application/json):**
```json
{
    "nama": "Dewi Lestari - Updated"
}
```

**Response (200 OK):**
```json
{
    "nama": "Dewi Lestari - Updated"
}
```

---

### 14. Delete Penulis [AUTH]

**DELETE** `/penulis/:id`

Menghapus penulis (hanya user yang login).

**Headers:**
```
Authorization: Bearer <token>
```

**Parameters:**
- `id` (path): ID penulis

**Response (200 OK):**
```json
{
    "id": "2"
}
```

---

## 📖 Buku Endpoints

### 15. Get All Buku

**GET** `/buku`

Mendapatkan daftar semua buku.

**Response (200 OK):**
```json
[
    {
        "id": 10,
        "kategori_id": 7,
        "penulis_id": 2,
        "judul": "Laskar Pelangi",
        "deskripsi": "Novel tentang anak-anak pelangi dan mimpi mereka",
        "cover": "laskar-cover.jpg",
        "file_pdf": "laskar-pelangi.pdf",
        "tahun_terbit": "2005",
        "created_at": "2026-07-06 05:31:16",
        "updated_at": "2026-07-06 05:31:16"
    }
]
```

---

### 16. Get Detail Buku

**GET** `/buku/:id`

Mendapatkan detail buku berdasarkan ID.

**Parameters:**
- `id` (path): ID buku

**Response (200 OK):**
```json
{
    "id": 10,
    "kategori_id": 7,
    "penulis_id": 2,
    "judul": "Laskar Pelangi",
    "deskripsi": "Novel tentang anak-anak pelangi dan mimpi mereka",
    "cover": "laskar-cover.jpg",
    "file_pdf": "laskar-pelangi.pdf",
    "tahun_terbit": "2005",
    "created_at": "2026-07-06 05:31:16",
    "updated_at": "2026-07-06 05:31:16"
}
```

**Error (404 Not Found):**
```json
{
    "message": "Buku tidak ditemukan"
}
```

---

### 17. Create Buku [AUTH]

**POST** `/buku`

Membuat buku baru dengan upload cover & file PDF (hanya user yang login).

**Headers:**
```
Authorization: Bearer <token>
Content-Type: multipart/form-data
```

**Request Body (form-data):**
| Key | Type | Value |
|-----|------|-------|
| `judul` | text | Pemrograman PHP |
| `deskripsi` | text | Belajar PHP dari dasar |
| `kategori_id` | text | 2 |
| `penulis_id` | text | 5 |
| `tahun_terbit` | text | 2024 |
| `cover` | file | (pilih file gambar) |
| `file_pdf` | file | (pilih file PDF) |

**Response (201 Created):**
```json
{
    "judul": "Pemrograman PHP",
    "deskripsi": "Belajar PHP dari dasar",
    "kategori_id": 2,
    "penulis_id": 5,
    "tahun_terbit": 2024,
    "cover": "1783290123-book-cover.jpg",
    "file_pdf": "1783290123-pemrograman-php.pdf"
}
```

**Error (400 Bad Request):**
```json
{
    "message": "Judul, kategori_id, dan penulis_id wajib diisi"
}
```

---

### 18. Update Buku [AUTH]

**POST** `/buku/:id`

Mengupdate buku (file cover & PDF opsional) (hanya user yang login).

> Note: menggunakan **POST** bukan PUT karena support multipart/form-data

**Headers:**
```
Authorization: Bearer <token>
Content-Type: multipart/form-data
```

**Parameters:**
- `id` (path): ID buku

**Request Body (form-data):**
| Key | Type | Value | Required |
|-----|------|-------|----------|
| `judul` | text | Judul baru | ✓ |
| `deskripsi` | text | Deskripsi baru | ❌ |
| `kategori_id` | text | ID kategori | ✓ |
| `penulis_id` | text | ID penulis | ✓ |
| `tahun_terbit` | text | Tahun terbit | ✓ |
| `cover` | file | File gambar baru | ❌ |
| `file_pdf` | file | File PDF baru | ❌ |

**Response (200 OK):**
```json
{
    "judul": "Judul baru",
    "deskripsi": "Deskripsi baru",
    "kategori_id": 2,
    "penulis_id": 5,
    "tahun_terbit": 2024,
    "cover": "1783290456-book-cover.jpg"
}
```

**Error (404 Not Found):**
```json
{
    "message": "Buku tidak ditemukan"
}
```

---

### 19. Delete Buku [AUTH]

**DELETE** `/buku/:id`

Menghapus buku beserta file cover & PDF-nya (hanya user yang login).

**Headers:**
```
Authorization: Bearer <token>
```

**Parameters:**
- `id` (path): ID buku

**Response (200 OK):**
```json
{
    "id": "10"
}
```

**Error (404 Not Found):**
```json
{
    "message": "Buku tidak ditemukan"
}
```

---

## Error Codes

| Status | Message | Penyebab |
|--------|---------|---------|
| 400 | Bad Request | Input data tidak valid |
| 401 | Unauthorized | Token tidak ada / tidak valid |
| 403 | Forbidden | Akses ditolak (role tidak sesuai) |
| 404 | Not Found | Resource tidak ditemukan |
| 500 | Internal Server Error | Erro database atau server |

---

## Security

✅ Password hashing menggunakan **bcrypt** (PHP native `password_hash`)
✅ JWT token menggunakan **firebase/php-jwt v7.1**
✅ Validasi input di semua endpoint
✅ Foreign key constraint pada relasi kategori & penulis
✅ Token expire setelah 24 jam

---

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at DATETIME,
    updated_at DATETIME
);
```

### Kategori Table
```sql
CREATE TABLE kategori (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100)
);
```

### Penulis Table
```sql
CREATE TABLE penulis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100)
);
```

### Buku Table
```sql
CREATE TABLE buku (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kategori_id INT,
    penulis_id INT,
    judul VARCHAR(255),
    deskripsi TEXT,
    cover VARCHAR(255),
    file_pdf VARCHAR(255),
    tahun_terbit YEAR,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE,
    FOREIGN KEY (penulis_id) REFERENCES penulis(id) ON DELETE CASCADE
);
```

---

## Testing

**Postman Collection**: `DLMS-Sprint-W3.postman_collection.json`

Import collection di Postman untuk testing semua endpoint.

---

## Version

- **Version**: 1.0.0
- **Sprint**: W3 (Sprint 3)
- **Last Updated**: 2026-07-06
- **Status**: ✅ Complete
