<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesainMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $designs = DB::table('desain_rumah')->get(['id', 'material_digunakan']);
        $materials = DB::table('materials')->get(['id', 'nama_material'])->keyBy('nama_material');
        
        $batch = [];
        $batchSize = 500;
        $now = now();
        $processedPairs = [];

        foreach ($designs as $design) {
            if (empty($design->material_digunakan)) {
                continue;
            }

            $parsedMaterials = $this->parseMaterials($design->material_digunakan);

            foreach ($parsedMaterials as $parsed) {
                $materialId = $this->findMaterialId($parsed['material'], $materials);
                
                if (!$materialId) {
                    continue;
                }

                $pairKey = "{$design->id}:{$materialId}";
                if (in_array($pairKey, $processedPairs)) {
                    continue;
                }
                $processedPairs[] = $pairKey;

                $batch[] = [
                    'desain_rumah_id' => $design->id,
                    'material_id' => $materialId,
                    'quantity' => $parsed['quantity'],
                    'unit' => $parsed['unit'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($batch) >= $batchSize) {
                    DB::table('desain_material')->insertOrIgnore($batch);
                    $batch = [];
                }
            }
        }

        if (!empty($batch)) {
            DB::table('desain_material')->insertOrIgnore($batch);
        }

        $this->command->info('DesainMaterial: ' . DB::table('desain_material')->count() . ' relationships seeded.');
    }

    private function parseMaterials(string $materialString): array
    {
        $result = [];
        $items = explode(';', $materialString);

        foreach ($items as $item) {
            $item = trim($item);
            if (empty($item)) {
                continue;
            }

            if (strpos($item, ':') === false) {
                continue;
            }

            [$materialName, $quantityStr] = explode(':', $item, 2);
            $materialName = trim($materialName);
            $quantityStr = trim($quantityStr);

            if (empty($materialName) || empty($quantityStr)) {
                continue;
            }

            // Parse quantity and unit (e.g., "2529kg" -> qty: 2529, unit: "kg")
            preg_match('/^(\d+(?:\.\d+)?)\s*([a-zA-Z\s]*)$/', $quantityStr, $matches);

            if (!empty($matches)) {
                $quantity = (int) $matches[1];
                $unit = trim($matches[2] ?? 'unit');
                if (empty($unit)) {
                    $unit = 'unit';
                }
            } else {
                $quantity = 1;
                $unit = 'unit';
            }

            $result[] = [
                'material' => $materialName,
                'quantity' => $quantity,
                'unit' => $unit,
            ];
        }

        return $result;
    }

    private function findMaterialId(string $materialName, $materials): ?int
    {
        $materialName = trim($materialName);

        // Direct match
        if ($materials->has($materialName)) {
            return $materials->get($materialName)->id;
        }

        // Fuzzy match: try substring matching and similarity
        $bestMatch = null;
        $bestScore = 0;

        foreach ($materials as $dbMaterial) {
            $dbName = $dbMaterial->nama_material;
            
            // Check if one contains the other
            if (stripos($dbName, $materialName) !== false || 
                stripos($materialName, $dbName) !== false) {
                $score = 0.9;
            } else {
                // Levenshtein distance based matching
                $distance = levenshtein(strtolower($materialName), strtolower($dbName));
                $maxLength = max(strlen($materialName), strlen($dbName));
                $score = 1 - ($distance / $maxLength);
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $dbMaterial;
            }
        }

        // Only accept matches with score > 0.7
        if ($bestScore > 0.7 && $bestMatch) {
            return $bestMatch->id;
        }

        return null;
    }
}
