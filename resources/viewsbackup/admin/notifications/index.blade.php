@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Notification History') }}</h1>
    <p>{{ __('A log of all notifications sent by the system.') }}</p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('User') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Message') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Sent At') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notifications as $notification)
                <tr>
                    <td>{{ $notification->id }}</td>
                    <td>
                        @if($notification->user)
                            <a href="{{ route('admin.users.show', $notification->user) }}">{{ $notification->user->name }}</a>
                        @else
                            {{ __('N/A') }}
                        @endif
                    </td>
                    <td>{{ $notification->title }}</td>
                    <td>{{ Str::limit($notification->message, 70) }}</td>
                    <td><span class="badge bg-{{ $notification->type }}">{{ $notification->type }}</span></td>
                    <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">{{ __('No notifications found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $notifications->links('pagination::bootstrap-5') }}
</div>
@endsection
