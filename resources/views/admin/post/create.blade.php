@extends('layouts.admin')
@section('title', lang( 'Add Post' ))

@section('styles')

@endsection

@section( 'content' )
    <form action="{{ route( 'admin.post.store' ) }}" method="post">
        <div id="content">
            <section id="widget-grid" class="">
                <div class="row" id="user-profile">
                    <article class="col-sm-12 col-md-9 sortable-grid ui-sortable">
                        {!! $form !!}
                        {!! $seo_form !!}
                    </article>
                    <article class="col-sm-12 col-md-3 sortable-grid ui-sortable">
                        {!! widget_before( [
                            'title' => lang( 'Publish' ),
                            'color' => 'success',
                        ] ) !!}
                        {!! $settings_form !!}
                        {!! widget_after() !!}
                        {!! $categories_form !!}
                        {!! widget_before( [
                            'title' => lang( 'Image' ),
                            'color' => 'info',
                        ] ) !!}
                        {!! make_media_uploader(['name'=>'image']) !!}
                        {!! widget_after() !!}
                    </article>
                </div>
            </section>
        </div>
    </form>
@stop
<?php
global $lang;
?>
@section('scripts')
    <script src="https://cdn.tiny.cloud/1/u5kezbdrfk4cdqgk1aouwsv8h4kg43firl96nnelw49jj1um/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init( {
            selector: ".tinymce-textarea",
            height: 500,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,

            @if ( $lang == 'fa_IR' )
            language_url: '{{ admin_assets_url() }}/app-assets/vendors/js/editors/tinymce2/langs/fa_IR.js',
            language: 'fa_IR',
            directionality: 'rtl',
            @endif

            plugins: [
                "autolink autosave link image hr anchor",
                "searchreplace wordcount visualblocks visualchars code insertdatetime media nonbreaking",
                "table contextmenu directionality template textcolor textcolor colorpicker textpattern",
            ],
            //
            toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

            // menubar: false,
        } );
    </script>
    <style>
        #categories-form-card .card-content{
            max-height: 300px;
            overflow-y: auto;
            direction: ltr;
        }
        #categories-form-card .card-body{
            direction: {{ $lang == 'fa_IR' ? 'rtl' : 'ltr' }};
        }
    </style>
@stop
