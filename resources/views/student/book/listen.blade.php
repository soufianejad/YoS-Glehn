<!-- resources/views/student/book/listen.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-4">
                        <a href="{{ route('student.book.show', $book->slug) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('Retour aux détails du livre') }}
                        </a>
                    </div>

                    <div class="text-center mb-4">
                        <h1 class="card-title h2">{{ $book->title }}</h1>
                        <p class="text-muted">{{ __('par') }} <a href="{{ route('public.author.show', $book->author) }}" class="text-decoration-none text-muted">{{ $book->author->name }}</a></p>
                    </div>

                    @if($book->audio_file)
                        <audio id="audioPlayer" controls class="w-100" controlsList="nodownload">
                            <source src="{{ asset('storage/' . $book->audio_file) }}" type="audio/mpeg">
                            {{ __("Votre navigateur ne supporte pas l'élément audio.") }}
                        </audio>

                        <div class="mt-3 text-center">
                            <span class="text-muted small">Progression : <span id="progress-percentage">0</span>%</span>
                            <div class="progress mt-1" style="height: 5px;">
                                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning text-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ __("Aucun fichier audio n'est disponible pour ce livre.") }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const audioElement = document.getElementById('audioPlayer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const saveProgressUrl = "{{ route('student.audio-progress', $book) }}";
    const initialPosition = {{ $initialPosition ?? 0 }};
    const progressPercentageEl = document.getElementById('progress-percentage');
    const progressBarEl = document.getElementById('progress-bar');

    let saveInterval;

    function updateProgressUI() {
        if (!audioElement || isNaN(audioElement.duration) || audioElement.duration === 0) return;
        const percentage = Math.round((audioElement.currentTime / audioElement.duration) * 100);
        if (progressPercentageEl) progressPercentageEl.textContent = percentage;
        if (progressBarEl) {
            progressBarEl.style.width = percentage + '%';
            progressBarEl.setAttribute('aria-valuenow', percentage);
        }
    }

    function saveProgress() {
        if (!audioElement || isNaN(audioElement.duration) || audioElement.duration === 0) {
            return;
        }

        fetch(saveProgressUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                current_position: Math.round(audioElement.currentTime),
                total_duration: Math.round(audioElement.duration)
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Progress saved:', data.message);
        })
        .catch(error => {
            console.error('Error saving progress:', error);
        });
    }

    if (audioElement) {
        audioElement.addEventListener('loadedmetadata', () => {
            audioElement.currentTime = initialPosition;
            updateProgressUI();
        });

        audioElement.addEventListener('timeupdate', updateProgressUI);

        audioElement.addEventListener('play', () => {
            clearInterval(saveInterval);
            saveInterval = setInterval(saveProgress, 15000);
        });

        audioElement.addEventListener('pause', () => {
            clearInterval(saveInterval);
            saveProgress();
        });

        audioElement.addEventListener('ended', () => {
            clearInterval(saveInterval);
            saveProgress();
        });

        window.addEventListener('beforeunload', () => {
            if (audioElement.currentTime > 0 && !audioElement.paused) {
                saveProgress();
            }
        });
    }
});
</script>
@endpush
