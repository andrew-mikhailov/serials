@extends('dashboard.layouts.main')

@section('title', 'Admin | Dashboard')

@section('dashboard_content')
<div class="page-title">
    <span class="title">Video list</span>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="card">
            <div class="card-header">

                <div class="card-title">
                    <div class="title">Videos</div>
                    <div class="login-button text-center">
                        <a class="btn btn-primary" href="{{ route('video.create') }}">Add Video</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="datatable table table-striped" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Quality</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Quality</th>
                        <th>Actions</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    @foreach ($videos as $video)
                    <tr>
                        <td>{{ $video->id }}</td>
                        <td>
                            <a href="{{ route('video.file', ['id' => $video->id]) }}" target="_blank">{{ $video->title }}</a>
                        </td>
                        <td>{{ $video->quality }}</td>
                        <td>
                            <a href="{{ route('video.edit', ['id' => $video->id]) }}">Edit</a>
                            {{ Form::open([
                                'url' => route('video.delete', ['id' => $video->id]),
                                'method' => 'DELETE'
                            ]) }}
                                <button type="submit" class="btn btn-danger btn-mini">Delete</button>
                            {{ Form::close() }}
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop