@extends('layouts.app')

@section('title', __('All Notifications'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">{{ __('Notifications') }}</h2>
        @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('notifications.markAllRead.web') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-check-double me-1"></i> {{ __('Mark all as read') }}
                </button>
            </form>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        <div class="list-group-item list-group-item-action p-4 {{ $notification->is_read ? 'bg-light opacity-75' : 'bg-white border-start border-primary border-4' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h5 class="mb-0 fw-bold {{ $notification->is_read ? 'text-muted' : 'text-dark' }}">
                                            {{ $notification->title }}
                                        </h5>
                                        @if(!$notification->is_read)
                                            <span class="badge bg-primary ms-2 rounded-pill" style="font-size: 0.6rem;">NEW</span>
                                        @endif
                                    </div>
                                    <p class="mb-2 text-secondary">{{ $notification->message }}</p>
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i> {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                        @if($notification->link)
                                            <a href="{{ $notification->link }}" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold">
                                                {{ __('View details') }} <i class="fas fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @if(!$notification->is_read)
                                    <button class="btn btn-light btn-sm rounded-circle mark-as-read-btn" 
                                            data-id="{{ $notification->id }}"
                                            title="{{ __('Mark as read') }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-light mb-3"></i>
                            <p class="text-muted">{{ __('No notifications found.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.mark-as-read-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');
        const container = btn.closest('.list-group-item');

        $.ajax({
            url: `/api/notifications/${id}/mark-as-read`,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function() {
                container.removeClass('bg-white border-start border-primary border-4').addClass('bg-light opacity-75');
                container.find('h5').removeClass('text-dark').addClass('text-muted');
                container.find('.badge').remove();
                btn.fadeOut();
                
                // Refresh top bell counter
                if (typeof fetchNotifications === 'function') {
                    fetchNotifications();
                }
            }
        });
    });
});
</script>
@endpush
