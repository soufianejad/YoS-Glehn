@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <h1>{{ __('Pending Reviews') }}</h1>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Reviews Awaiting Approval') }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-list"></i> {{ __('All Reviews') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Book') }}</th>
                        <th>{{ __('Rating') }}</th>
                        <th>{{ __('Comment') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr>
                        <td>{{ $review->id }}</td>
                        <td>{{ $review->user->name }}</td>
                        <td><a href="{{ route('book.show', $review->book->slug) }}" target="_blank">{{ $review->book->title }}</a></td>
                        <td>{{ $review->rating }}/5</td>
                        <td>{{ Str::limit($review->comment, 50) }}</td>
                        <td>
                            <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> {{ __('View & Approve') }}
                            </a>
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this review?') }}');" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> {{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">{{ __('No pending reviews found.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
