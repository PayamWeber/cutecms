<?php

use App\Models\Media;

$files = Media::get_latest_files();
?>
<div id="media-modal">
    <div class="media-modal-bg"></div>
    <div class="this-container">
        <div class="hidden-close-bg"></div>
        <a class="close-modal"><i class="fa fa-close"></i></a>
        <div id="fileuploader">{{ lang( 'Upload' ) }}</div>
        {!! $files !!}
    </div>
    <img src="{{ admin_assets_url( '/assets/img/loader.svg' ) }}" class="this-loader">
    <div class="image-size-select">
        @if( Media::$image_size_names )
            <p class="this-caption">{{ lang('Choose size') }}</p>
            <ul>
                @foreach( Media::$image_size_names as $key => $value )
                    <li data-size-name="{{ $key }}">{{ $value }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
