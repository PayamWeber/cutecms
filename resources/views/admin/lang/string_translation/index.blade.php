@extends('layouts.admin')
@section('title', lang('String Translation'))

@section( 'content' )
    <div id="content">
        <section id="widget-grid" class="">
            <div class="row">
                <article class="col-sm-12 sortable-grid ui-sortable">
                    @include('admin.lang.string_translation.list')
                </article>
            </div>
        </section>
    </div>
@stop

<?php global $lang ?>
@section('scripts')
    <script>
        var _xhr;
        $( document ).ready( function () {
            $( 'body' ).on( 'click', '.udpate-string', function () {
                var _this = $( this );
                _this.addClass( 'disabled' ).find( '.la' ).addClass( 'la-spin la-spinner' ).removeClass( 'la-save' );
                if ( _xhr )
                {
                    _xhr.abort();
                    _xhr = null;
                }
                _xhr = $.ajax( {
                    'method': 'post',
                    'url': '{{ route('admin.string_translation.update_string') }}',
                    'dataType': 'JSON',
                    'data': {
                        _token: '{{ csrf_token() }}',
                        string: _this.data( 'string' ),
                        text_domain: _this.data( 'text-domain' ),
                        translation: _this.parent().parent().find( 'input' ).val(),
                        lang: '{{ \Request::get('lang', $lang) }}',
                    }
                } ).done( function ( data ) {
                    if ( data.type == 'success' )
                    {
                        setTimeout(function () {
                            _this.removeClass( 'disabled' ).find( '.la' ).removeClass( 'la-spin la-spinner' ).addClass( 'la-save' );
                        }, 300)
                    }
                } ).fail( function () {
                    swal( {
                        title: '{{ lang("Problem with saving data") }}',
                        icon: "error",
                        showCancelButton: true,
                        buttons: {
                            confirm: {
                                text: '{{ lang("OK") }}',
                                value: true,
                                visible: true,
                                className: "",
                                closeModal: true
                            }
                        }
                    } );
                    _this.removeClass( 'disabled' ).find( '.la' ).removeClass( 'la-spin la-spinner' ).addClass( 'la-save' );
                } );
            } )

            $('#change-list-lang').change(function () {
                $('#quick-filter-form').submit();
            })
        } )
    </script>
@stop
