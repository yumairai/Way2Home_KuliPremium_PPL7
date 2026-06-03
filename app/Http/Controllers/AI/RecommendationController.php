<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\MLRecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    private MLRecommendationService $mlService;

    public function __construct(MLRecommendationService $mlService)
    {
        $this->mlService = $mlService;
    }

    /**
     * Show input preferensi page
     */
    public function inputPreference()
    {
        return view('input_preferensi_ai');
    }

    /**
     * Get ML-powered recommendations via form submission
     *
     * Algoritma: K-Nearest Neighbors (KNN) dengan Weighted Euclidean Distance
     */
    public function getRecommendations(Request $request)
    {
        $validated = $request->validate([
            'location'    => 'required|string',
            'style'       => 'required|string',
            'area'        => 'required|integer|min:25|max:350',
            'bedrooms'    => 'required|integer|min:1',
            'bathrooms'   => 'required|integer|min:1',
            'garage'      => 'required|integer|min:0',
            'quality'     => 'required|integer|min:1|max:10',
            'budget'      => 'required|integer|min:100000000|max:2000000000',
            'ac_required' => 'required|boolean',
            'priority'    => 'required|in:biaya,estetik,cepat',
            'flexibility' => 'required|numeric|min:0|max:50',
        ]);

        // Jalankan KNN ML recommendation
        $recommendations = $this->mlService->recommend($validated, k: 3);

        // Simpan ke session untuk halaman hasil
        session(['ai_recommendations' => $recommendations]);
        session(['ai_preferences'     => $validated]);
        session(['ml_algorithm_info'  => [
            'algorithm'      => 'K-Nearest Neighbors (KNN)',
            'distance'       => 'Weighted Euclidean Distance',
            'normalization'  => 'Min-Max Scaling',
            'k'              => 3,
            'priority'       => $validated['priority'],
            'total_features' => 6,
        ]]);

        return redirect()->route('rekomendasi.hasil');
    }

    /**
     * Show hasil rekomendasi ML
     */
    public function showResults()
    {
        $recommendations = session('ai_recommendations', []);
        $preferences     = session('ai_preferences', []);
        $algorithmInfo   = session('ml_algorithm_info', []);

        return view('rekomendasi_rumah', compact('recommendations', 'preferences', 'algorithmInfo'));
    }
}
