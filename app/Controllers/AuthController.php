<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;

class AuthController extends ResourceController
{
    public function register()
    {
        $model = new UserModel();
        $data  = $this->request->getJSON(true) ?? [];

        if (empty($data['nama']) || empty($data['email']) || empty($data['password'])) {
            return $this->fail('Nama, email, dan password wajib diisi', 400);
        }
        // Sebelumnya format email dan panjang password tidak divalidasi sama sekali.
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->fail('Format email tidak valid', 400);
        }
        if (strlen($data['password']) < 6) {
            return $this->fail('Password minimal 6 karakter', 400);
        }
        if ($model->where('email', $data['email'])->first()) {
            return $this->fail('Email sudah terdaftar', 400);
        }

        $model->insert([
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'     => 'user',
        ]);

        return $this->respondCreated(['message' => 'Akun berhasil dibuat']);
    }

    public function login()
    {
        $model = new UserModel();
        $data  = $this->request->getJSON(true) ?? [];
        $user  = $model->where('email', $data['email'] ?? '')->first();

        if (!$user || !password_verify($data['password'] ?? '', $user['password'])) {
            return $this->failUnauthorized('Email atau password salah');
        }

        $payload = [
            'id'   => $user['id'],
            'role' => $user['role'],
            'exp'  => time() + 86400,
        ];
        $token = JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');

        return $this->respond([
            'message' => 'Login berhasil',
            'token'   => $token,
            'role'    => $user['role'],
        ]);
    }

    public function logout()
    {
        return $this->respond(['message' => 'Logout berhasil']);
    }

    public function profile()
    {
        $model = new UserModel();
        $user = $model->select('id, nama, email, role')->find($this->request->user->id);
        return $this->respond($user);
    }
}