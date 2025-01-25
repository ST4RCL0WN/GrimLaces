@extends('admin.layout')

@section('admin-title')
    Emotes
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Emotes' => 'admin/emotes']) !!}

    <h1>Emotes</h1>

    <p>This is a list of emotes players/admins can use in the tinymce editor (and in comments).</p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/emotes/create') }}"><i class="fas fa-plus"></i> Create New Emote</a>
    </div>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    @if (!count($emotes))
        <p>No emotes found.</p>
    @else
        {!! $emotes->render() !!}
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-5 col-md-6">
                        <div class="logs-table-cell">Name</div>
                    </div>
                    <div class="col-5 col-md-5">
                        <div class="logs-table-cell">Description</div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($emotes as $emote)
                    <div class="logs-table-row">
                        <div class="row flex-wrap">
                            <div class="col-5 col-md-6">
                                <div class="logs-table-cell">
                                    @if (!$emote->is_released)
                                        <i class="fas fa-eye-slash mr-1"></i>
                                    @endif
                                    {{ $emote->name }}
                                </div>
                            </div>
                            <div class="col-4 col-md-5">
                                <div class="logs-table-cell">{{ $emote->description ? $emote->description : 'No description' }}</div>
                            </div>
                            <div class="col-3 col-md-1 text-right">
                                <div class="logs-table-cell">
                                    <a href="{{ url('admin/data/emotes/edit/' . $emote->id) }}" class="btn btn-primary py-0 px-2">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {!! $emotes->render() !!}
    @endif

@endsection

@section('scripts')
    @parent
@endsection
