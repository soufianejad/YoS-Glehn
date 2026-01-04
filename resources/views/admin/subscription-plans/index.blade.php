<!-- resources/views/admin/subscription-plans/index.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Subscription Plan Management') }}</h1>

    <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary mb-3">{{ __('Create New Plan') }}</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Price') }}</th>
                <th>Duration (Days)</th>
                <th>{{ __('Active') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plans as $plan)
                <tr>
                    <td>{{ $plan->id }}</td>
                    <td>{{ $plan->name }}</td>
                    <td>{{ $plan->type }}</td>
                    <td>${{ $plan->price }}</td>
                    <td>{{ $plan->duration_days }}</td>
                    <td>{{ $plan->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('admin.subscription-plans.edit', $plan) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.subscription-plans.destroy', $plan) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">{{ __('Delete') }}</button>
                        </form>
                        @if($plan->is_active)
                            <form action="{{ route('admin.subscription-plans.deactivate', $plan) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary">{{ __('Deactivate') }}</button>
                            </form>
                        @else
                            <form action="{{ route('admin.subscription-plans.activate', $plan) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">{{ __('Activate') }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $plans->links('pagination::bootstrap-5') }}
</div>
@endsection
