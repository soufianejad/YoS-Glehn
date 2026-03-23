{{-- resources/views/book/listen.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-4">
                        <a href="{{ route('book.show', $book->slug) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('Retour aux détails du livre') }}
                        </a>
                    </div>
                    
                    <div class="text-center mb-4">
                        <h1 class="card-title h2">{{ $book->title }}</h1>
                        <p class="text-muted">{{ __('par') }} {{ $book->author->name }}</p>
                    </div>

                    @if($book->files->where('file_type', 'audio')->count() > 0 || $book->audio_file)
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-6">
                                <form action="{{ route('listen.book', $book->slug) }}" method="GET" id="language-form">
                                    <div class="input-group input-group-sm">
                                        <label class="input-group-text bg-primary text-white border-primary" for="file_id">
                                            <i class="fas fa-language me-1"></i> {{ __('Langue Audio') }}
                                        </label>
                                        <select name="file_id" id="file_id" class="form-select border-primary text-center" onchange="this.form.submit()">
                                            @if($book->audio_file)
                                                <option value="default" {{ $fileId == 'default' ? 'selected' : '' }}>
                                                    {{ __('Par défaut') }}
                                                </option>
                                            @endif
                                            @foreach($book->files->where('file_type', 'audio') as $file)
                                                <option value="{{ $file->id }}" {{ $fileId == $file->id ? 'selected' : '' }}>
                                                    {{ strtoupper($file->language) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if($audioPath)
                        <audio id="audioPlayer" controls class="w-100" controlsList="nodownload">
                            <source src="{{ route('listen.audio.content', $book) }}?file_id={{ $fileId }}" type="audio/mpeg">
                            {{ __("Votre navigateur ne supporte pas l'élément audio.") }}
                        </audio>
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
    const saveProgressUrl = "{{ route('listen.progress', $book) }}";
    const initialPosition = {{ $initialPosition ?? 0 }};
    
    let saveInterval;

    function saveProgress() {
        if (!audioElement || isNaN(audioElement.duration) || audioElement.duration === 0) {
            return;
        }

        fetch(saveProgressUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
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
        // Définir la position de départ une fois que les métadonnées sont chargées
        audioElement.addEventListener('loadedmetadata', () => {
            audioElement.currentTime = initialPosition;
        });

        // Sauvegarder la progression toutes les 15 secondes
        audioElement.addEventListener('play', () => {
            clearInterval(saveInterval);
            saveInterval = setInterval(saveProgress, 15000);
        });

        // Arrêter l'intervalle lorsque la lecture est en pause ou terminée
        audioElement.addEventListener('pause', () => {
            clearInterval(saveInterval);
            saveProgress();
        });

        audioElement.addEventListener('ended', () => {
            clearInterval(saveInterval);
            saveProgress();
        });

        // Sauvegarder avant de quitter la page
        window.addEventListener('beforeunload', (event) => {
            if (audioElement.currentTime > 0 && !audioElement.paused) {
                saveProgress();
            }
        });
    }
});
</script>
@endpush
