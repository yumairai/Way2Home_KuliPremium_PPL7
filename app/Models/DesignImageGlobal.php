<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignImageGlobal extends Model
{
    protected $table = 'design_images_global';

    protected $fillable = [
        'kategori',
        'urutan',
        'path_gambar',
        'deskripsi',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Get image for a specific kategori (style)
     */
    public static function getImageByCategory(string $kategori, int $position = 1): ?string
    {
        $image = self::where('kategori', $kategori)
            ->where('urutan', $position)
            ->first();

        return $image?->path_gambar;
    }

    /**
     * Get random image from kategori
     */
    public static function getRandomImageByCategory(string $kategori): ?string
    {
        $image = self::where('kategori', $kategori)
            ->inRandomOrder()
            ->first();

        return $image?->path_gambar;
    }
}
