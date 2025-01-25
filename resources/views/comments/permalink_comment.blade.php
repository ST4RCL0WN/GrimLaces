@extends('layouts.app')

@section('title')
    Comments
@endsection

@section('profile-title')
    Comment
@endsection

@section('content')
    <h1>Comments on {!! $comment->commentable_type == 'App\Models\User\UserProfile' ? $comment->commentable->user->displayName : $comment->commentable->displayName !!}</h1>
    <h5>
        @if (count($comment->children))
            <a href="{{ url('comment/') . '/' . $comment->endOfThread->id }}" class="btn btn-secondary btn-sm mr-2">Go To End Of Thread</a>
        @endif
        @if (isset($comment->child_id))
            <a href="{{ url('comment/') . '/' . $comment->child_id }}" class="btn btn-secondary btn-sm mr-2">See Parent</a>
            <a href="{{ url('comment/') . '/' . $comment->topComment->id }}" class="btn btn-secondary btn-sm mr-2">Go To Top Comment</a>
        @endif
    </h5>

    <hr class="mb-3">
    <div class="d-flex mw-100 row mx-0" style="overflow:hidden;">
        @include('comments._perma_comments', ['comment' => $comment, 'limit' => 0, 'depth' => 0])
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            tinymce.init({
                selector: '.comment-wysiwyg',
                height: 300,
                menubar: false,
                convert_urls: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen spoiler',
                    'insertdatetime media table paste code help wordcount toc',
                    'textpattern',
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | toc bullist numlist outdent indent | link image | spoiler-add spoiler-remove | removeformat | code',
                content_css: [
                    '{{ asset('css/app.css') }}',
                    '{{ asset('css/lorekeeper.css') }}'
                ],
                spoiler_caption: 'Toggle Spoiler',
                target_list: false,
                toc_class: 'container',
                textpattern_patterns: [{
                        start: '# ',
                        format: 'h1'
                    },
                    {
                        start: '## ',
                        format: 'h2'
                    },
                    {
                        start: '### ',
                        format: 'h3'
                    },
                    {
                        start: '#### ',
                        format: 'h4'
                    },
                    {
                        start: '##### ',
                        format: 'h5'
                    },
                    {
                        start: '###### ',
                        format: 'h6'
                    },
                    {
                        start: '**',
                        end: '**',
                        format: 'bold'
                    },
                    {
                        start: '__',
                        end: '__',
                        format: 'bold'
                    },
                    {
                        start: '*',
                        end: '*',
                        format: 'italic'
                    },
                    {
                        start: '_',
                        end: '_',
                        format: 'italic'
                    },
                    {
                        start: '~~',
                        end: '~~',
                        format: 'strikethrough'
                    },
                    {
                        start: '> ',
                        format: 'blockquote'
                    },
                    {
                        start: '* ',
                        cmd: 'InsertUnorderedList'
                    },
                    {
                        start: '- ',
                        cmd: 'InsertUnorderedList'
                    },
                    {
                        start: '+ ',
                        cmd: 'InsertUnorderedList'
                    },
                    {
                        start: '1. ',
                        cmd: 'InsertOrderedList'
                    },
                ],
                mentions: {
                    delimiter: JSON.parse('{!! json_encode(config('lorekeeper.mentions.delimiters')) !!}'),
                    source: function(query, process, delimiter) {
                        $.get('{{ url('mentions') }}', {
                            query: query,
                            delimiter: delimiter
                        }, function(data) {
                            process(data);
                        });
                    },
                    highlighter: function(text) {
                        //make matched block strong (make case insensitive)
                        return text.replace(new RegExp('(' + this.query + ')', 'ig'), function($1, match) {
                            return '<strong>' + match + '</strong>';
                        });
                    },
                    insert: function(item) {
                        let content = item.mention_display_name;
                        const editor = tinyMCE.activeEditor;
                        editor.insertContent(content + '&#8203;')

                        const rng = editor.selection.getRng();
                        rng.setStart(rng.endContainer, rng.endOffset);
                        rng.collapse(true);
                        editor.selection.setRng(rng);

                        return '';
                    },
                    render: function(item) {
                        return '<li class="pl-2">' +
                            '<a href="javascript:;">' +
                            (item.image ? '<img src="' + item.image + '" class="rounded mr-1" style="height: 25px; width: 25px;" />' : '') +
                            '<span>' + item.name + '</span>' +
                            '</a>' +
                            '</li>';
                    },
                },
            });
        });
    </script>
@endsection
