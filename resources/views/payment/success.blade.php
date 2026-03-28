@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow border-0 rounded-4 p-5">
                <div class="mb-4 text-success animate__animated animate__bounceIn">
                    <i class="fas fa-check-circle fa-5x"></i>
                </div>
                <h2 class="fw-bold mb-3">{{ __('Paiement réussi !') }}</h2>
                <p class="text-muted mb-4">{{ __('Votre achat a été validé avec succès. Vous pouvez maintenant accéder à votre contenu.') }}</p>

                @if($lastPayment && ($lastPayment->payment_type === 'book_pdf' || $lastPayment->payment_type === 'book_purchase') && $lastPayment->book)
                    @php
                        $isDownloadable = $lastPayment->book->is_downloadable;
                        $downloadUrl = route('book.secure_download', $lastPayment->book->slug);
                    @endphp

                    @if($isDownloadable)
                        <div class="alert alert-info py-3 mb-4 rounded-3 border-0">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('Le téléchargement de votre livre va débuter automatiquement dans quelques secondes.') }}
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <a href="{{ $downloadUrl }}" class="btn btn-success btn-lg rounded-pill px-5 shadow-sm" id="autoDownloadBtn">
                                <i class="fas fa-download me-2"></i> {{ __('Télécharger le PDF') }}
                            </a>
                            <a href="{{ route('read.book', $lastPayment->book->slug) }}" class="btn btn-outline-primary btn-lg rounded-pill px-5">
                                <i class="fas fa-book-open me-2"></i> {{ __('Lire en ligne') }}
                            </a>
                        </div>

                        @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Auto-download after 3 seconds
                                setTimeout(function() {
                                    window.location.href = "{{ $downloadUrl }}";
                                }, 3000);
                            });
                        </script>
                        @endpush
                    @else
                        <div class="d-grid gap-2 mb-3">
                            <a href="{{ route('read.book', $lastPayment->book->slug) }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                                <i class="fas fa-book-open me-2"></i> {{ __('Lire mon livre maintenant') }}
                            </a>
                        </div>
                    @endif
                @else
                    <a href="{{ $url }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                        <i class="fas fa-arrow-right me-2"></i> {{ __('Accéder au contenu') }}
                    </a>
                @endif
                
                <div class="mt-4">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted small">
                        {{ __('Aller à mon tableau de bord') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
