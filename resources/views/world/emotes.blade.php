@extends('world.layout')

@section('title')
    Emotes
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Emotes' => 'world/emotes']) !!}
    <h1>Emotes</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Search by Name']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    <p>Emotes that you can use on world pages or the tinymce editor.</p>


    {!! $emotes->render() !!}
    <div class="row">
        @foreach ($emotes as $emote)
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
                            <div class="card-body bg-light mb-0">
                                In the rich text editor:
                                <div class="alert alert-secondary">
                                    :{{ $emote->name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {!! $emotes->render() !!}

    <div class="text-center mt-4 small text-muted">{{ count($emotes) }} result{{ count($emotes) == 1 ? '' : 's' }} found.</div>
@endsection
