<?php

return [

    // the max file upload size
    'max_upload_size' => env( 'MAX_UPLOAD_SIZE', 10485760 ),

    // the fake url for files that being uploaded
    'media_fake_url' => env( 'MEDIA_FAKE_URL' ),

    // user panel application url
    'panel_url' => env( 'PANEL_URL', 'http://localhost/' ),

];
