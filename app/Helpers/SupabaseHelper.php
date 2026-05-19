<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class SupabaseHelper
{
    /**
     * Generate public Supabase URL for a file in Materials bucket
     *
     * @param string $filePath
     * @return string
     */
    public static function getPublicUrl(string $filePath): string
    {
        $projectId = env('AWS_ACCESS_KEY_ID');
        $bucket = env('SUPABASE_BUCKET', 'Materials');
        
        return sprintf(
            'https://%s.supabase.co/storage/v1/object/public/%s/%s',
            $projectId,
            $bucket,
            $filePath
        );
    }

    /**
     * Upload file to Supabase storage
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @return string|null
     */
    public static function uploadFile($file, string $folder = 'materials'): ?string
    {
        try {
            $filename = time() . '-' . $file->getClientOriginalName();
            
            $path = Storage::disk('s3')->putFileAs(
                $folder,
                $file,
                $filename,
                'public'
            );
            
            return $path;
        } catch (\Exception $e) {
            logger()->error('Supabase upload error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get full public URL after upload
     *
     * @param string $filePath
     * @return string
     */
    public static function getUploadedFileUrl(string $filePath): string
    {
        return self::getPublicUrl($filePath);
    }

    /**
     * Delete file from Supabase
     *
     * @param string $filePath
     * @return bool
     */
    public static function deleteFile(string $filePath): bool
    {
        try {
            return Storage::disk('s3')->delete($filePath);
        } catch (\Exception $e) {
            logger()->error('Supabase delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * List files in a folder
     *
     * @param string $folder
     * @return array
     */
    public static function listFiles(string $folder = 'materials'): array
    {
        try {
            return Storage::disk('s3')->files($folder);
        } catch (\Exception $e) {
            logger()->error('Supabase list error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if file exists in Supabase
     *
     * @param string $filePath
     * @return bool
     */
    public static function fileExists(string $filePath): bool
    {
        try {
            return Storage::disk('s3')->exists($filePath);
        } catch (\Exception $e) {
            logger()->error('Supabase exists check error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file size
     *
     * @param string $filePath
     * @return int|null
     */
    public static function getFileSize(string $filePath): ?int
    {
        try {
            return Storage::disk('s3')->size($filePath);
        } catch (\Exception $e) {
            logger()->error('Supabase size check error: ' . $e->getMessage());
            return null;
        }
    }
}
