@extends('layouts.admin')
@section('title', lang('Media'))
@section('body_class', 'page-media')

@section( 'content' )
    <div id="content">
        <section id="widget-grid" class="">
            <div class="row">
                <article class="col-sm-12 sortable-grid ui-sortable">
                    @include('admin.media.list')
                </article>
            </div>
        </section>
    </div>
@stop

@section('scripts')
    <script>
        $( 'body' ).on( 'click', '.ajax-file-upload-preview', function () {
            window.location = '{{ route('admin.media.index') }}/' + $(this).parent().data('file-id') + '/edit';
        } )
    </script>
@stop
