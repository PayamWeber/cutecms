<?php

use App\Models\Media;

$store_url        = route( 'admin.media.file_store' );
$delete_url       = route( 'admin.media.file_store' );
$refresh_url      = route( 'admin.media.refresh_list' );
$max_file_size    = Media::MAX_FILE_SIZE;
$csrf             = csrf_token();
$image_crop_sizes = json_encode( Media::IMAGE_CROP_SIZES );
?>
<script >
    var _file_uploader_data = {
        store_url: '{{ $store_url }}',
        delete_url: '{{ $delete_url }}',
        refresh_url: '{{ $refresh_url }}',
        max_file_size: '{{ $max_file_size }}',
        token: '{{ $csrf }}',
        image_crop_sizes: '{{ $image_crop_sizes }}'
    }
</script>
<script type="text/javascript" src="{{ admin_assets_url() }}/assets/js/jquery.fileuploader.min.js"></script>
<script type="text/javascript" src="{{ admin_assets_url() }}/assets/js/media.js"></script>
