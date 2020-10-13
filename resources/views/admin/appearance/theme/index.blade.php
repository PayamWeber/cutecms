@extends('layouts.admin')
@section('title', lang('Themes'))

@section( 'styles' )
    <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/css-rtl/pages/gallery.css">
    <style>
        .grid-item.active, .grid-item.active .card{
            background-color:#e6e6e6;
        }
        .grid-item.active{
            background-color: #666ee8;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
        }
        .grid-item.active .card{
            padding: 10px;
        }
    </style>
@stop
@section( 'content' )
    <div id="content">
        <section id="widget-grid" class="">
            <div class="row">
                <article class="col-sm-12 sortable-grid ui-sortable">
                    @include('admin.appearance.theme.list')
                </article>
            </div>
        </section>
    </div>
@stop

@section('scripts')
    <script src="{{ admin_assets_url() }}/app-assets/vendors/js/gallery/masonry/masonry.pkgd.min.js"
            type="text/javascript"></script>
    <script>
        $( 'document' ).ready( function () {
            // execute above function
            if ( $( '.masonry-grid' ).length > 0 )
            {
                $( '.masonry-grid' ).masonry( {
                    // options
                    itemSelector: '.grid-item',
                    columnWidth: 100,
                    RTL: true
                    //cpercentPosition: true
                } );
            }
        } );
    </script>
@stop
