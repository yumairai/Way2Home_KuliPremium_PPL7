@php
    $flashMessages = [];

    if (session('success')) {
        $flashMessages[] = [
            'variant' => 'success',
            'title' => 'Berhasil',
            'message' => session('success'),
        ];
    }

    if (session('error')) {
        $flashMessages[] = [
            'variant' => 'error',
            'title' => 'Gagal',
            'message' => session('error'),
        ];
    }

    if (session('message')) {
        $flashMessages[] = [
            'variant' => 'success',
            'title' => 'Informasi',
            'message' => session('message'),
        ];
    }

    if ($errors->any()) {
        $flashMessages[] = [
            'variant' => 'error',
            'title' => 'Mohon Periksa Kembali',
            'message' => collect($errors->all())->implode("\n"),
        ];
    }
@endphp

@if (!empty($flashMessages))
    <script>
        window.W2HFlashMessages = @json($flashMessages);
    </script>
@endif
