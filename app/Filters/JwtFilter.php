<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            return service('response')->setJSON(['message' => 'Token tidak ditemukan'])->setStatusCode(401);
        }

        $token  = str_replace('Bearer ', '', $authHeader);
        $secret = getenv('JWT_SECRET');

        // Jika JWT_SECRET belum di-set di .env, Key() akan melempar TypeError
        // (bukan Exception), sehingga sebelumnya bisa membuat aplikasi fatal error
        // alih-alih membalas JSON 401 yang rapi.
        if (empty($secret)) {
            return service('response')->setJSON(['message' => 'JWT_SECRET belum dikonfigurasi di server'])->setStatusCode(500);
        }

        try {
            $decoded       = JWT::decode($token, new Key($secret, 'HS256'));
            $request->user = $decoded;
        } catch (\Throwable $e) {
            return service('response')->setJSON(['message' => 'Token tidak valid atau kedaluwarsa'])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}