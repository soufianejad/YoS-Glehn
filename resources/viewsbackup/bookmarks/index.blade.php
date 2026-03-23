@extends('layouts.dashboard')

@section('title', 'Mes Marque-pages')
@section('header', 'Mes Marque-pages')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ __('Tous mes marque-pages') }}</h4>
    </div>
    <div class="card-body">
        <div class="list-group" id="bookmarks-list-container">
            @forelse ($bookmarks as $bookmark)
                <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="mb-2 mb-md-0">
                        <a href="{{ route('read.book', ['book' => $bookmark->book, 'page' => $bookmark->page_number]) }}" class="text-decoration-none">
                            <div class="fw-bold">{{ $bookmark->title }}</div>
                        </a>
                        <small class="text-muted">
                            Page {{ $bookmark->page_number }} dans <em>{{ $bookmark->book->title }}</em>
                        </small>
                    </div>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary edit-bookmark-btn" 
                                data-bookmark-id="{{ $bookmark->id }}" 
                                data-bookmark-title="{{ $bookmark->title }}">
                            <i class="fas fa-pencil-alt me-1"></i> {{ __('Modifier') }}
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-bookmark-btn" 
                                data-bookmark-id="{{ $bookmark->id }}">
                            <i class="fas fa-trash me-1"></i> {{ __('Supprimer') }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-muted">
                    {{ __("Vous n'avez aucun marque-page pour le moment.") }}
                </div>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $bookmarks->links() }}
        </div>
    </div>
</div>

<!-- Modal pour modifier un marque-page -->
<div class="modal fade" id="edit-bookmark-modal" tabindex="-1" aria-labelledby="editBookmarkModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editBookmarkModalLabel">{{ __('Modifier le marque-page') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-bookmark-form">
            <input type="hidden" id="edit-bookmark-id">
            <div class="mb-3">
                <label for="edit-bookmark-title" class="form-label">{{ __('Titre') }}</label>
                <input type="text" class="form-control" id="edit-bookmark-title" required>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
        <button type="submit" form="edit-bookmark-form" class="btn btn-primary">{{ __('Enregistrer') }}</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bookmarksListContainer = document.getElementById('bookmarks-list-container');
    const editModal = new bootstrap.Modal(document.getElementById('edit-bookmark-modal'));
    const editForm = document.getElementById('edit-bookmark-form');
    const editBookmarkIdInput = document.getElementById('edit-bookmark-id');
    const editBookmarkTitleInput = document.getElementById('edit-bookmark-title');

    bookmarksListContainer.addEventListener('click', async function (e) {
        const target = e.target;
        const editButton = target.closest('.edit-bookmark-btn');
        const deleteButton = target.closest('.delete-bookmark-btn');

        // Gérer la modification
        if (editButton) {
            const bookmarkId = editButton.dataset.bookmarkId;
            const bookmarkTitle = editButton.dataset.bookmarkTitle;
            
            editBookmarkIdInput.value = bookmarkId;
            editBookmarkTitleInput.value = bookmarkTitle;
            editModal.show();
        }

        // Gérer la suppression
        if (deleteButton) {
            const bookmarkId = deleteButton.dataset.bookmarkId;
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce marque-page ?')) return;

            try {
                const response = await fetch(`/bookmarks/${bookmarkId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to delete bookmark.');
                
                deleteButton.closest('.list-group-item').remove();
            } catch (error) {
                console.error(error);
                alert('An error occurred while deleting the bookmark.');
            }
        }
    });

    // Gérer la soumission du formulaire de modification
    editForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const bookmarkId = editBookmarkIdInput.value;
        const newTitle = editBookmarkTitleInput.value;

        try {
            const response = await fetch(`/bookmarks/${bookmarkId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ title: newTitle })
            });

            if (!response.ok) throw new Error('Failed to update bookmark.');
            
            // Mettre à jour l'UI sans recharger la page
            const listItem = bookmarksListContainer.querySelector(`[data-bookmark-id="${bookmarkId}"]`).closest('.list-group-item');
            listItem.querySelector('.fw-bold').textContent = newTitle;
            listItem.querySelector('.edit-bookmark-btn').dataset.bookmarkTitle = newTitle;

            editModal.hide();
        } catch (error) {
            console.error(error);
            alert('An error occurred while updating the bookmark.');
        }
    });
});
</script>
@endpush
