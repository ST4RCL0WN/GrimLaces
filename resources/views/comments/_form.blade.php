<div class="{{ isset($compact) && !$compact ? 'card' : '' }} mt-3">
    <div class="{{ isset($compact) && !$compact ? 'card-body' : '' }}">
        {!! Form::open(['url' => 'comments/make/' . base64_encode(urlencode(get_class($model))) . '/' . $model->getKey()]) !!}
        <input type="hidden" name="type" value="{{ isset($type) ? $type : null }}" />
        <div class="form-group">
            {!! Form::label('message', 'Enter your message here:') !!}
            {!! Form::textarea('message', null, ['class' => 'form-control comment-wysiwyg', 'rows' => 5]) !!}
            <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown cheatsheet.</a></small>
        </div>

        {!! Form::submit('Submit', ['class' => 'btn btn-sm btn-outline-success text-uppercase']) !!}
        {!! Form::close() !!}
    </div>
</div>
<br />
