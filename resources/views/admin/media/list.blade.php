{!! widget_before( [
    'title' => lang( 'Media' ),
    'color' => 'white',
] ) !!}
<div class="this-container">
    <div class="hidden-close-bg"></div>
    <a class="close-modal"><i class="fa fa-close"></i></a>
    <div id="fileuploader" class="position-relative">{{ lang( 'Upload' ) }}</div>
    {!! \App\Models\Media::get_latest_files() !!}
</div>
{!! widget_after() !!}
