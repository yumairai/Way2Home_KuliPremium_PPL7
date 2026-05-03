<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SupabaseStorageService
{
    // ─── PUBLIC ASSETS (foto material, banner, dll) ───────────────────

    public function uploadPublic(UploadedFile $file, string $folder): string
    {
        $path = $folder . '/' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public_assets')->put($path, file_get_contents($file), 'public');
        return $this->publicUrl('public-assets', $path);
    }

    public function deletePublic(string $publicUrl): void
    {
        $path = $this->extractPath($publicUrl, 'public-assets');
        if ($path) Storage::disk('public_assets')->delete($path);
    }

    // ─── USER PRIVATE (dokumen, invoice, dll) ─────────────────────────

    public function uploadPrivate(UploadedFile $file, int $userId, string $folder): string
    {
        $path = $userId . '/' . $folder . '/' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

        $result = Storage::disk('user_private')->put($path, file_get_contents($file));

        // ❌ kalau gagal upload
        if (!$result) {
            throw new \Exception('Upload ke Supabase gagal (put() return false)');
        }

        // 🔥 cek benar-benar ada
        if (!Storage::disk('user_private')->exists($path)) {
            throw new \Exception('File tidak ditemukan setelah upload');
        }

        return $path;
    }

    public function getSignedUrl(string $path, int $expiresIn = 3600): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
            'apikey'        => env('SUPABASE_SERVICE_ROLE_KEY'),
        ])->post(env('SUPABASE_URL') . '/storage/v1/object/sign/user-private/' . $path, [
            'expiresIn' => $expiresIn
        ]);

        return rtrim(env('SUPABASE_URL'), '/') . '/storage/v1' . $response->json('signedURL');
    }

    public function deletePrivate(string $path): void
    {
        Storage::disk('user_private')->delete($path);
    }

    // ─── ADMIN ACCESS (bisa akses file siapapun) ──────────────────────

    public function getAdminSignedUrl(string $path, string $bucket = 'user-private', int $expiresIn = 3600): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
            'apikey'        => env('SUPABASE_SERVICE_ROLE_KEY'),
        ])->post(env('SUPABASE_URL') . '/storage/v1/object/sign/' . $bucket . '/' . $path, [
            'expiresIn' => $expiresIn
        ]);

        return rtrim(env('SUPABASE_URL'), '/') . '/storage/v1' . $response->json('signedURL');
    }

    public function getAdminSignedUrls(array $paths, string $bucket = 'user-private', int $expiresIn = 3600): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
            'apikey'        => env('SUPABASE_SERVICE_ROLE_KEY'),
        ])->post(env('SUPABASE_URL') . '/storage/v1/object/sign/' . $bucket, [
            'expiresIn' => $expiresIn,
            'paths'     => $paths,
        ]);

        $result = [];
        foreach ($response->json() as $item) {
            if (isset($item['signedURL'])) {
                $result[$item['path']] = rtrim(env('SUPABASE_URL'), '/') . '/storage/v1' . $item['signedURL'];
            }
        }
        return $result;
    }

    // ─── HELPER ───────────────────────────────────────────────────────

    private function publicUrl(string $bucket, string $path): string
    {
        return rtrim(env('SUPABASE_URL'), '/') . '/storage/v1/object/public/' . $bucket . '/' . $path;
    }

    private function extractPath(string $url, string $bucket): ?string
    {
        preg_match('/\/storage\/v1\/object\/public\/' . $bucket . '\/(.+)/', $url, $matches);
        return $matches[1] ?? null;
    }
}