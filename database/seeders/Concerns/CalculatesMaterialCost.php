<?php

namespace Database\Seeders\Concerns;

use App\Models\Material;
use RuntimeException;

trait CalculatesMaterialCost
{
    /**
     * @return array<string, int>
     */
    protected function loadMaterialPriceMap(): array
    {
        $priceMap = Material::query()
            ->pluck('harga', 'nama_material')
            ->mapWithKeys(fn ($harga, $nama) => [trim((string) $nama) => (int) $harga])
            ->all();

        if (empty($priceMap)) {
            throw new RuntimeException('Data material kosong. Jalankan MaterialSeeder terlebih dahulu.');
        }

        return $priceMap;
    }

    /**
     * Format material_digunakan:
     * "Nama Material:123unit; Material Lain:10pcs"
     *
     * @param array<string, int> $materialPriceMap
     */
    protected function calculateMaterialCost(
        string $materialDigunakan,
        array $materialPriceMap,
        int|string|null $rowIdentifier = null
    ): int {
        if (trim($materialDigunakan) === '') {
            return 0;
        }

        $total = 0;
        $context = $rowIdentifier !== null ? " pada baris ID {$rowIdentifier}" : '';

        foreach (explode(';', $materialDigunakan) as $item) {
            $item = trim($item);

            if ($item === '') {
                continue;
            }

            $parts = explode(':', $item, 2);
            if (count($parts) !== 2) {
                throw new RuntimeException("Format material tidak valid{$context}: {$item}");
            }

            $namaMaterial = trim($parts[0]);
            $qtyWithUnit = trim($parts[1]);

            if (!preg_match('/^(\d+)\s*([A-Za-z0-9\/ ]+)$/', $qtyWithUnit, $matches)) {
                throw new RuntimeException("Format kuantitas material tidak valid{$context}: {$item}");
            }

            $qty = (int) $matches[1];

            if (!array_key_exists($namaMaterial, $materialPriceMap)) {
                throw new RuntimeException("Harga material '{$namaMaterial}' tidak ditemukan{$context}.");
            }

            $total += $qty * $materialPriceMap[$namaMaterial];
        }

        return $total;
    }
}
