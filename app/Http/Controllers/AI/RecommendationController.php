<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    /**
     * Show input preferensi page
     */
    public function inputPreference()
    {
        return view('input_preferensi_ai');
    }

    /**
     * Get recommendations via form submission
     */
    public function getRecommendations(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string',
            'style' => 'required|string',
            'area' => 'required|integer|min:30|max:350',
            'bedrooms' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:1',
            'garage' => 'required|integer|min:0',
            'quality' => 'required|integer|min:1|max:10',
            'budget' => 'required|integer|min:100000000|max:2000000000',
            'ac_required' => 'required|boolean',
            'priority' => 'required|in:biaya,estetik,cepat',
            'flexibility' => 'required|numeric|min:0|max:50',
        ]);

        // Get property data
        $properties = $this->getPropertyDatabase();

        // Filter berdasarkan constraints
        $filtered = $this->filterByConstraints($properties, $validated);

        // Score menggunakan AI logic dengan prioritas
        $scored = $this->scoreProperties($filtered, $validated);

        // Sort dan ambil top 3
        $recommendations = array_slice($scored, 0, 3);

        // Add image URLs
        $recommendations = $this->addImages($recommendations);

        // Store di session untuk display di halaman results
        session(['ai_recommendations' => $recommendations]);
        session(['ai_preferences' => $validated]);

        return redirect()->route('rekomendasi.hasil');
    }

    /**
     * Show hasil rekomendasi
     */
    public function showResults()
    {
        $recommendations = session('ai_recommendations', []);
        $preferences = session('ai_preferences', []);

        return view('rekomendasi_rumah', [
            'recommendations' => $recommendations,
            'preferences' => $preferences
        ]);
    }

    /**
     * Get dummy property database
     */
    private function getPropertyDatabase()
    {
        // Simplified property database
        $properties = [];
        
        for ($i = 1; $i <= 50; $i++) {
            $properties[] = [
                'id' => $i,
                'price' => mt_rand(100000000, 2000000000),
                'area' => mt_rand(30, 350),
                'rooms' => mt_rand(1, 5),
                'bathrooms' => mt_rand(1, 4),
                'garage' => mt_rand(0, 3),
                'quality' => mt_rand(5, 10),
                'ac' => (bool)mt_rand(0, 1),
                'style' => ['Minimalist', 'Modern', 'Mewah'][mt_rand(0, 2)],
                'location' => ['Bandung Barat', 'Bandung Timur'][mt_rand(0, 1)],
                'construction_time' => mt_rand(3, 18),
            ];
        }

        return $properties;
    }

    /**
     * Filter properties berdasarkan constraints user
     */
    private function filterByConstraints($properties, $prefs)
    {
        $flexibility = ($prefs['flexibility'] / 100) * $prefs['budget'];
        $minBudget = $prefs['budget'] - $flexibility;
        $maxBudget = $prefs['budget'] + $flexibility;

        $filtered = array_filter($properties, function($p) use ($prefs, $minBudget, $maxBudget) {
            return $p['price'] >= $minBudget &&
                   $p['price'] <= $maxBudget &&
                   $p['area'] >= ($prefs['area'] - 20) &&
                   $p['area'] <= ($prefs['area'] + 20) &&
                   $p['rooms'] >= $prefs['bedrooms'] &&
                   $p['bathrooms'] >= $prefs['bathrooms'] &&
                   $p['garage'] >= $prefs['garage'] &&
                   $p['quality'] >= $prefs['quality'] &&
                   (!$prefs['ac_required'] || $p['ac'] == true) &&
                   strtolower($p['style']) == strtolower($prefs['style']) &&
                   strtolower($p['location']) == strtolower($prefs['location']);
        });

        return array_values($filtered);
    }

    /**
     * Score properties dengan prioritas user
     */
    private function scoreProperties($properties, $prefs)
    {
        $weights = [
            'biaya' => [
                'price' => 0.4,
                'quality' => 0.2,
                'space' => 0.2,
                'comfort' => 0.1,
                'value' => 0.1
            ],
            'estetik' => [
                'price' => 0.15,
                'quality' => 0.35,
                'space' => 0.2,
                'comfort' => 0.2,
                'value' => 0.1
            ],
            'cepat' => [
                'price' => 0.2,
                'quality' => 0.2,
                'space' => 0.15,
                'comfort' => 0.15,
                'value' => 0.3
            ]
        ];

        $w = $weights[$prefs['priority']];

        foreach ($properties as &$p) {
            $priceScore = max(0, 1 - (abs($p['price'] - $prefs['budget']) / $prefs['budget']));
            $qualityScore = $p['quality'] / 10;
            $spaceScore = max(0, 1 - (abs($p['area'] - $prefs['area']) / 350));
            $comfortScore = $p['ac'] ? 1 : 0.5;
            $valueScore = $qualityScore * (1 - $priceScore);

            $p['score'] = round(($priceScore * $w['price'] +
                               $qualityScore * $w['quality'] +
                               $spaceScore * $w['space'] +
                               $comfortScore * $w['comfort'] +
                               $valueScore * $w['value']) * 100, 1);

            $p['estimate_time'] = $p['construction_time'];
        }

        usort($properties, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $properties;
    }

    /**
     * Add generated image URLs
     */
    private function addImages($recommendations)
    {
        $imageKeywords = [
            'Minimalist' => 'minimalist house architecture modern',
            'Modern' => 'modern house design contemporary',
            'Mewah' => 'luxury house design elegant'
        ];

        foreach ($recommendations as &$rec) {
            // Generate consistent image URL based on property characteristics
            $seed = $rec['id'] . $rec['area'] . $rec['rooms'];
            $hash = abs(crc32($seed)) % 1000;
            
            // Use Picsum Photos with seeded random
            $rec['image_url'] = "https://picsum.photos/400/300?random={$hash}";
            
            // Backup fallback
            if (empty($rec['image_url'])) {
                $rec['image_url'] = "https://via.placeholder.com/400x300/2B3361/FFFFFF?text=" . urlencode($rec['style']);
            }
        }

        return $recommendations;
    }
}
