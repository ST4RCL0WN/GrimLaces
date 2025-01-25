@extends('admin.layout')

@section('admin-title')
    Emotes
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Emotes' => 'admin/emotes', ($emote->id ? 'Edit' : 'Create') . ' Emote' => $emote->id ? 'admin/emotes/edit/' . $emote->id : 'admin/emotes/create']) !!}

    <h1>{{ $emote->id ? 'Edit' : 'Create' }} Emote
        @if ($emote->id)
            <a href="#" class="btn btn-outline-danger float-right delete-emote-button">Delete Emote</a>
        @endif
    </h1>

    {!! Form::open(['url' => $emote->id ? 'admin/emotes/edit/' . $emote->id : 'admin/emotes/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-3 form-group">
            {!! Form::label('Image (Required)') !!}
            <div>{!! Form::file('image') !!}</div>
            @if ($emote->id)
                <div class="form-check">
                    {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                    {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
                </div>
            @endif
        </div>
        <div class="col-md-9 form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $emote->name, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Alt Text') !!}
        {!! Form::text('alt_text', $emote->alt_text, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('description') !!}
        {!! Form::textarea('description', $emote->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="form-group">
        {!! Form::checkbox('is_active', 1, $emote->id ? $emote->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, this emote will not be able to be used') !!}
    </div>

    <div class="text-right">
        {!! Form::submit($emote->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($emote->id && $emote->imageUrl)
        <h3>Preview</h3>
        <div class="col-12 col-md-4 col-sm-">
            <div class="card h-100">
                <div class="card-header border-bottom-0">
                    <x-admin-edit title="Emote" :object="$emote" />
                    <div class="world-entry-image">
                        {!! $emote->getImage() !!}
                    </div>
                    <h3 class="mb-2 text-center">
                        {!! $emote->name !!}
                    </h3>
                    <div>
                        {!! $emote->description !!}
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5>Use This Emote</h5>
                        </div>
                        <div class="card-body bg-light">
                            In the rich text editor:
                            <div class="alert alert-secondary mb-0">
                                :{{ $emote->name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.selectize').selectize();
            $('.delete-emote-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/emotes/delete') }}/{{ $emote->id }}", 'Delete Emote');
            });
        });
    </script>
@endsection
