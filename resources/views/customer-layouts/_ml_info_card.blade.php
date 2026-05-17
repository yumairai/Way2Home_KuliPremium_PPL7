{{--
    Partial: _ml_info_card.blade.php
    Cara pakai: @include('customer-layouts._ml_info_card')
    Pastikan $algorithmInfo dan $recommendations tersedia di view.
--}}

{{-- ===== ML ALGORITHM INFO BADGE ===== --}}
@if(!empty($algorithmInfo))
<div class="ml-info-card" style="
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border: 1px solid #0f3460;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 28px;
    color: #e0e0e0;
    font-family: 'Courier New', monospace;
">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
        <span style="font-size:22px;">🤖</span>
        <h4 style="margin:0; color:#64ffda; font-size:1rem; letter-spacing:1px;">
            MACHINE LEARNING ENGINE
        </h4>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:12px; font-size:0.82rem;">
        <div>
            <span style="color:#888;">Algoritma</span><br>
            <strong style="color:#fff;">{{ $algorithmInfo['algorithm'] ?? '-' }}</strong>
        </div>
        <div>
            <span style="color:#888;">Fungsi Jarak</span><br>
            <strong style="color:#fff;">{{ $algorithmInfo['distance'] ?? '-' }}</strong>
        </div>
        <div>
            <span style="color:#888;">Normalisasi</span><br>
            <strong style="color:#fff;">{{ $algorithmInfo['normalization'] ?? '-' }}</strong>
        </div>
        <div>
            <span style="color:#888;">Jumlah Fitur (K-dim)</span><br>
            <strong style="color:#fff;">{{ $algorithmInfo['total_features'] ?? '-' }} fitur</strong>
        </div>
        <div>
            <span style="color:#888;">K (Neighbors)</span><br>
            <strong style="color:#fff;">{{ $algorithmInfo['k'] ?? 3 }} rekomendasi</strong>
        </div>
        <div>
            <span style="color:#888;">Prioritas Bobot</span><br>
            <strong style="color:#64ffda; text-transform:uppercase;">{{ $algorithmInfo['priority'] ?? '-' }}</strong>
        </div>
    </div>

    <div style="margin-top:14px; padding-top:14px; border-top:1px solid #0f3460; font-size:0.75rem; color:#666; line-height:1.6;">
        <strong style="color:#888;">Formula:</strong>
        d(u,p) = √( Σ w<sub>i</sub> · (u<sub>i</sub> − p<sub>i</sub>)² )
        &nbsp;|&nbsp;
        <strong style="color:#888;">Similarity Score:</strong>
        S = 1 / (1 + d) × 100
    </div>
</div>
@endif

{{-- ===== FEATURE MATCH PER REKOMENDASI ===== --}}
@foreach($recommendations as $idx => $rec)
    @if(!empty($rec['ml_feature_match']))
    <div class="ml-feature-match" data-rec="{{ $idx }}" style="
        background: #0d1117;
        border: 1px solid #21262d;
        border-radius: 8px;
        padding: 14px 18px;
        margin-top: 10px;
        font-size: 0.78rem;
    ">
        <div style="color:#58a6ff; margin-bottom:8px; font-weight:600;">
            📊 Analisis Kemiripan Fitur — {{ $rec['tipe_rumah'] }}
            <span style="float:right; color:#3fb950; font-size:1rem;">
                ML Score: <strong>{{ $rec['ml_score'] ?? '-' }}</strong>
            </span>
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:8px;">
            @foreach($rec['ml_feature_match'] as $feat)
            <div style="
                background: #161b22;
                border-radius: 4px;
                padding: 6px 10px;
                min-width: 110px;
            ">
                <div style="color:#8b949e; margin-bottom:4px;">{{ $feat['label'] }}</div>
                <div style="
                    background: #21262d;
                    border-radius: 3px;
                    height: 6px;
                    overflow: hidden;
                ">
                    <div style="
                        width: {{ $feat['match'] }}%;
                        height: 100%;
                        background: {{ $feat['match'] >= 80 ? '#3fb950' : ($feat['match'] >= 50 ? '#d29922' : '#f85149') }};
                        border-radius: 3px;
                    "></div>
                </div>
                <div style="color:#c9d1d9; font-weight:600; margin-top:2px;">{{ $feat['match'] }}%</div>
            </div>
            @endforeach
        </div>
        <div style="color:#484f58; margin-top:8px; font-size:0.7rem;">
            Euclidean Distance: {{ $rec['ml_distance'] ?? '-' }}
        </div>
    </div>
    @endif
@endforeach
