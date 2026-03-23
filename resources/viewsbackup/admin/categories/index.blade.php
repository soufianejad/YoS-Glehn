<!-- resources/views/admin/categories/index.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Category Management') }}</h1>

    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">{{ __('Add New Category') }}</a>

    <table class="table table-hover" id="categories-table">
        <thead>
            <tr>
                <th style="width: 50px;"></th>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Space') }}</th>
                <th>{{ __('Order') }}</th>
                <th>{{ __('Active') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody id="sortable-categories">
            @foreach($categories as $category)
                <tr data-id="{{ $category->id }}">
                    <td class="handle" style="cursor: move;"><i class="fas fa-arrows-alt"></i></td>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->space }}</td>
                    <td class="order-column">{{ $category->order }}</td>
                    <td>{{ $category->is_active ? __('Yes') : __('No') }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __("Are you sure?") }}')">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $categories->links('pagination::bootstrap-5') }}
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('sortable-categories');
        var sortable = Sortable.create(el, {
            handle: '.handle',
            animation: 150,
            onUpdate: function (evt) {
                var order = [];
                var rows = el.querySelectorAll('tr');
                rows.forEach(function(row, index) {
                    order.push(row.getAttribute('data-id'));
                    row.querySelector('.order-column').textContent = index + 1;
                });

                fetch('{{ route("admin.categories.update-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(response => response.json())
                .then(data => {
                    // You can add a success message here, e.g., using Toastr
                    console.log(data.message);
                })
                .catch(error => {
                    // You can add an error message here
                    console.error('Error:', error);
                });
            }
        });
    });
</script>
@endpush
