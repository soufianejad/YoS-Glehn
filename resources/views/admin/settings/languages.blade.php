@extends('layouts.admin')

@section('title', 'Manage Languages')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New Language</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.languages.update') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="language_code">Language Code</label>
                                    <input type="text" class="form-control" id="language_code" name="language_code" placeholder="e.g., en" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="language_name">Language Name</label>
                                    <input type="text" class="form-control" id="language_name" name="language_name" placeholder="e.g., English" required>
                                </div>
                                <div class="form-group col-md-4 d-flex align-items-end">
                                    <button type="submit" name="add_language" class="btn btn-primary">Add Language</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Available Languages</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($languages as $language)
                                    <tr>
                                        <td>{{ $language['code'] }}</td>
                                        <td>{{ $language['name'] }}</td>
                                        <td>
                                            <form action="{{ route('admin.settings.languages.update') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="language_code" value="{{ $language['code'] }}">
                                                <button type="submit" name="remove_language" class="btn btn-danger btn-sm">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
