<!-- resources/views/admin/users/adult-invitations.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Adult Access Invitations') }}</h1>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="{{ __('generateInvitationModal') }}">
        {{ __('Generate New Invitation') }}
    </button>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Token') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Created By') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Uses') }}</th>
                <th>{{ __('Max Uses') }}</th>
                <th>{{ __('Expires At') }}</th>
                <th>{{ __('Used By') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invitations as $invitation)
                <tr>
                    <td>{{ $invitation->access_token }}</td>
                    <td>{{ $invitation->email ?? 'N/A' }}</td>
                    <td>{{ $invitation->creator->name ?? 'N/A' }}</td>
                    <td>{{ $invitation->status }}</td>
                    <td>{{ $invitation->uses_count }}</td>
                    <td>{{ $invitation->max_uses }}</td>
                    <td>
                        {{ $invitation->expires_at ? \Carbon\Carbon::parse($invitation->expires_at)->format('Y-m-d H:i') : 'Never' }}
                    </td>
                    <td>{{ $invitation->user->name ?? 'N/A' }}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary copy-link-btn" data-link="{{ route('adult.invitation', ['token' => $invitation->access_token]) }}">{{ __('Copy Link') }}</button>
                        <form action="{{ route('admin.adult.revoke-invitation', $invitation->access_token) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to revoke this invitation?')">{{ __('Revoke') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $invitations->links('pagination::bootstrap-5') }}

    <!-- Generate Invitation Modal -->
    <div class="modal fade" id="generateInvitationModal" tabindex="-1" aria-labelledby="generateInvitationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateInvitationModalLabel">{{ __('Generate New Adult Invitation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.adult.generate-invitation') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email (Optional)</label>
                            <input type="email" class="form-control" id="email" name="email">
                            <div class="form-text">{{ __('If provided, this invitation can only be used by this email address.') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="max_uses" class="form-label">{{ __("Max Uses") }}</label>
                            <input type="number" class="form-control" id="max_uses" name="max_uses" value="1" min="1">
                        </div>
                        <div class="mb-3">
                            <label for="expires_at" class="form-label">Expires At (Optional)</label>
                            <input type="datetime-local" class="form-control" id="expires_at" name="expires_at">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Generate Invitation') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyLinkButtons = document.querySelectorAll('.copy-link-btn');

        copyLinkButtons.forEach(button => {
            button.addEventListener('click', function () {
                const link = this.dataset.link;
                const button = this;

                if (navigator.clipboard && window.isSecureContext) {
                    // Use Clipboard API in secure contexts
                    navigator.clipboard.writeText(link).then(() => {
                        showCopiedMessage(button);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                    });
                } else {
                    // Fallback for non-secure contexts
                    const textArea = document.createElement('textarea');
                    textArea.value = link;
                    textArea.style.position = 'fixed'; // Prevent scrolling to bottom of page in MS Edge.
                    textArea.style.top = '0';
                    textArea.style.left = '0';
                    textArea.style.opacity = '0';
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();

                    try {
                        const successful = document.execCommand('copy');
                        if (successful) {
                            showCopiedMessage(button);
                        }
                    } catch (err) {
                        console.error('Fallback: Oops, unable to copy', err);
                    }

                    document.body.removeChild(textArea);
                }
            });
        });

        function showCopiedMessage(button) {
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            setTimeout(() => {
                button.textContent = originalText;
            }, 2000);
        }
    });
</script>
@endpush
