/*! modernizr 3.6.0 (Custom Build) | MIT *
 * https://modernizr.com/download/?-mq-setclasses !*/
!function ( e, n, t ) {
    function o( e, n )
    {
        return typeof e === n
    }

    function a()
    {
        var e, n, t, a, s, i, r;
        for ( var l in d ) if ( d.hasOwnProperty( l ) )
        {
            if ( e = [], n = d[ l ], n.name && (e.push( n.name.toLowerCase() ), n.options && n.options.aliases && n.options.aliases.length) ) for ( t = 0; t < n.options.aliases.length; t++ ) e.push( n.options.aliases[ t ].toLowerCase() );
            for ( a = o( n.fn, "function" ) ? n.fn() : n.fn, s = 0; s < e.length; s++ ) i = e[ s ], r = i.split( "." ), 1 === r.length ? Modernizr[ r[ 0 ] ] = a : (!Modernizr[ r[ 0 ] ] || Modernizr[ r[ 0 ] ] instanceof Boolean || (Modernizr[ r[ 0 ] ] = new Boolean( Modernizr[ r[ 0 ] ] )), Modernizr[ r[ 0 ] ][ r[ 1 ] ] = a), f.push( (a ? "" : "no-") + r.join( "-" ) )
        }
    }

    function s( e )
    {
        var n = u.className, t = Modernizr._config.classPrefix || "";
        if ( p && (n = n.baseVal), Modernizr._config.enableJSClass )
        {
            var o = new RegExp( "(^|\\s)" + t + "no-js(\\s|$)" );
            n = n.replace( o, "$1" + t + "js$2" )
        }
        Modernizr._config.enableClasses && (n += " " + t + e.join( " " + t ), p ? u.className.baseVal = n : u.className = n)
    }

    function i()
    {
        return "function" != typeof n.createElement ? n.createElement( arguments[ 0 ] ) : p ? n.createElementNS.call( n, "http://www.w3.org/2000/svg", arguments[ 0 ] ) : n.createElement.apply( n, arguments )
    }

    function r()
    {
        var e = n.body;
        return e || (e = i( p ? "svg" : "body" ), e.fake = !0), e
    }

    function l( e, t, o, a )
    {
        var s, l, f, d, c = "modernizr", p = i( "div" ), m = r();
        if ( parseInt( o, 10 ) ) for ( ; o--; ) f = i( "div" ), f.id = a ? a[ o ] : c + (o + 1), p.appendChild( f );
        return s = i( "style" ), s.type = "text/css", s.id = "s" + c, (m.fake ? m : p).appendChild( s ), m.appendChild( p ), s.styleSheet ? s.styleSheet.cssText = e : s.appendChild( n.createTextNode( e ) ), p.id = c, m.fake && (m.style.background = "", m.style.overflow = "hidden", d = u.style.overflow, u.style.overflow = "hidden", u.appendChild( m )), l = t( p, e ), m.fake ? (m.parentNode.removeChild( m ), u.style.overflow = d, u.offsetHeight) : p.parentNode.removeChild( p ), !!l
    }

    var f = [], d = [], c = {
        _version: "3.6.0",
        _config: { classPrefix: "", enableClasses: !0, enableJSClass: !0, usePrefixes: !0 },
        _q: [],
        on: function ( e, n ) {
            var t = this;
            setTimeout( function () {
                n( t[ e ] )
            }, 0 )
        },
        addTest: function ( e, n, t ) {
            d.push( { name: e, fn: n, options: t } )
        },
        addAsyncTest: function ( e ) {
            d.push( { name: null, fn: e } )
        }
    }, Modernizr = function () {
    };
    Modernizr.prototype = c, Modernizr = new Modernizr;
    var u = n.documentElement, p = "svg" === u.nodeName.toLowerCase(), m = function () {
        var n = e.matchMedia || e.msMatchMedia;
        return n ? function ( e ) {
            var t = n( e );
            return t && t.matches || !1
        } : function ( n ) {
            var t = !1;
            return l( "@media " + n + " { #modernizr { position: absolute; } }", function ( n ) {
                t = "absolute" == (e.getComputedStyle ? e.getComputedStyle( n, null ) : n.currentStyle).position
            } ), t
        }
    }();
    c.mq = m, a(), s( f ), delete c.addTest, delete c.addAsyncTest;
    for ( var h = 0; h < Modernizr._q.length; h++ ) Modernizr._q[ h ]();
    e.Modernizr = Modernizr
}( window, document );

var _upload_type = 'file';
var _current_file;
var _last_files_upadated_type;
var _current_file_object;
$( document ).ready( function () {
    //    file uploader
    do_file_uploader();
    $( 'body' ).on( 'click', '.ajax-upload-dragdrop', function () {
        $( '.ajax-file-upload input[type="file"]' ).click();
    } );

    /**
     * remove a file from list
     */
    $( '.ajax-file-upload-container' ).on( 'click', '.ajax-file-upload-statusbar.past-file .ajax-file-upload-delete', function () {
        var _this = $( this ).parent();
        if ( _this.data( 'file-id' ) )
        {
            if ( $( 'body' ).hasClass( 'page-media' ) )
            {
                swal( {
                    title: lang.are_you_sure,
                    icon: "warning",
                    showCancelButton: true,
                    buttons: {
                        cancel: {
                            text: lang.no,
                            value: null,
                            visible: true,
                            className: "btn-warning",
                            closeModal: true,
                        },
                        confirm: {
                            text: lang.yes,
                            value: true,
                            visible: true,
                            className: "",
                            closeModal: true
                        }
                    }
                } ).then( function ( isConfirm ) {
                    if ( isConfirm )
                    {
                        var _file_id = _this.data( 'file-id' );
                        $.post( _file_uploader_data.store_url, {
                            _token: _file_uploader_data.token,
                            _method: 'DELETE',
                            file_id: _file_id
                        }, function ( resp, textStatus, jqXHR ) {

                        } );
                        _this.hide( 300 );
                        setTimeout( function () {
                            _this.remove();
                        }, 300 );
                    }
                } );
            } else
            {
                if ( window.confirm( lang.are_you_sure ) )
                {
                    var _file_id = _this.data( 'file-id' );
                    $.post( _file_uploader_data.store_url, {
                        _token: _file_uploader_data.token,
                        _method: 'DELETE',
                        file_id: _file_id
                    }, function ( resp, textStatus, jqXHR ) {

                    } );
                    _this.hide( 300 );
                    setTimeout( function () {
                        _this.remove();
                    }, 300 );
                }
            }
        }
    } );

    /**
     * open media modal
     */
    $( 'body' ).on( 'click', '.media-upload-buttons .open-media-modal-button', function ( e ) {
        e.preventDefault();
    } );
    $( window ).on( 'load', function () {
        $( 'body' ).on( 'click', '.media-upload-buttons .open-media-modal-button', function ( e ) {
            e.preventDefault();
            var _this = $( this );
            var _main = _this.parent().parent();
            _upload_type = _main.data( 'upload-type' );
            _current_file = _main.attr( 'id' );
            open_media_modal();
        } );
    } );

    /**
     * delete current media
     */
    $( 'body' ).on( 'click', '.media-upload-buttons .delete-current-media', function ( e ) {
        e.preventDefault();
        var _this = $( this );
        var _main = _this.parent().parent();
        var _type = _main.data( 'upload-type' );
        _main.find( 'input[type="hidden"]' ).val( '' );
        if ( _type == 'avatar' )
        {
            $( 'input[name="avatar_id"]' ).val( '' );
            $( '.avatar-image' ).attr( 'src', $( '.avatar-image' ).data( 'default-src' ) );
        }
        if ( _type == 'image' )
        {
            _main.find( 'img' ).attr( 'src', '' ).attr( 'alt', '' );
        }
        if ( _type == 'file' )
        {
            _main.find( 'p' ).text( '' );
        }
        _this.addClass( 'hidden' );
    } );

    /**
     * close modal
     */
    $( 'body' ).on( 'click', '#media-modal .close-modal, .media-modal-bg, #media-modal .hidden-close-bg', function () {
        close_media_modal();
    } );

    /**
     * select modal
     */
    $( 'body' ).on( 'click', '.ajax-file-upload-statusbar .ajax-file-upload-preview', function () {
        var _this = $( this ).parent();
        if ( _this.data( 'file-id' ) )
        {
            if ( _upload_type == 'avatar' )
            {
                var _file_id = _this.data( 'file-id' );
                var _file_thumbnail = _this.data( 'thumbnail' );
                close_media_modal();
                $( 'input[name="avatar_id"]' ).val( _file_id );
                $( '.avatar-image' ).attr( 'src', _file_thumbnail );
                $( '#' + _current_file + ' .delete-current-media' ).removeClass( 'hidden' );
            } else if ( _upload_type != 'tinymce' )
            {
                var _file_id = _this.data( 'file-id' );
                var _file_thumbnail = _this.data( 'thumbnail' );
                var _file_name = _this.data( 'file-name' );
                close_media_modal();
                $( '#' + _current_file + ' input[type="hidden"]' ).val( _file_id );
                if ( _upload_type == 'image' )
                    $( '#' + _current_file + ' img' ).attr( 'src', _file_thumbnail );
                if ( _upload_type == 'file' )
                    $( '#' + _current_file + ' p.file-name' ).text( _file_name );
                $( '#' + _current_file + ' .delete-current-media' ).removeClass( 'hidden' );
            }
            else
            {
                _current_file_object = _this;
                $( '#media-modal .this-container' ).removeClass( 'show-this' );
                open_image_size_select();
            }
        }
    } );

    /**
     * fixed upload area
     */
    if ( Modernizr.mq( '(min-width:992px)' ) )
    {
        fixed_upload_area();
        $( '#media-modal' ).scroll( function () {
            fixed_upload_area();
        } );
    }

    /**
     * image select size
     */
    $( '#media-modal .image-size-select ul li' ).click( function () {
        var _this = $( this );
        if ( _upload_type == 'tinymce' )
        {
            tinymce.activeEditor.execCommand( 'mceInsertContent', false,
                '<img src="' + _current_file_object.data( _this.data( 'size-name' ) ) + '" alt="' + _current_file_object.data( 'caption' ) + '">'
            );
            close_image_size_select();
            close_media_modal();
        }
    } )
} );

function do_file_uploader()
{
    $( "#fileuploader" ).uploadFile( {
        url: _file_uploader_data.store_url,
        dragDrop: true,
        fileName: "media_file",
        returnType: "json",
        showDelete: true,
        statusBarWidth: 200,
        showPreview: true,
        previewHeight: "40px",
        previewWidth: "40px",
        maxFileSize: _file_uploader_data.max_file_size,
        allowedTypes: "jpg,gif,png,svg,jpeg,ico,zip,rar,pdf,txt,mp4",
        formData: {
            _token: _file_uploader_data.token,
            _type: _upload_type
        },
        deleteCallback: function ( last_response, data ) {
            if ( data.statusbar.data( 'file-id' ) )
            {
                if ( $( 'body' ).hasClass( 'page-media' ) )
                {
                    swal( {
                        title: lang.are_you_sure,
                        icon: "warning",
                        showCancelButton: true,
                        buttons: {
                            cancel: {
                                text: lang.no,
                                value: null,
                                visible: true,
                                className: "btn-warning",
                                closeModal: true,
                            },
                            confirm: {
                                text: lang.yes,
                                value: true,
                                visible: true,
                                className: "",
                                closeModal: true
                            }
                        }
                    } ).then( function ( isConfirm ) {
                        if ( isConfirm )
                        {
                            var _file_id = data.statusbar.data( 'file-id' );
                            $.post( _file_uploader_data.store_url, {
                                _token: _file_uploader_data.token,
                                _method: 'DELETE',
                                file_id: _file_id
                            }, function ( resp, textStatus, jqXHR ) {
                                data.statusbar.remove();
                            } );
                        }
                    } );
                } else
                {
                    if ( window.confirm( lang.are_you_sure ) )
                    {
                        var _file_id = data.statusbar.data( 'file-id' );
                        $.post( _file_uploader_data.store_url, {
                            _token: _file_uploader_data.token,
                            _method: 'DELETE',
                            file_id: _file_id
                        }, function ( resp, textStatus, jqXHR ) {
                            data.statusbar.remove();
                        } );
                    }
                }
            }
        },
        onLoad: function () {
            // var _files = $( '.ajax-file-upload-container.latest-files' ).html();
            // $( 'div[class="ajax-file-upload-container"]' ).append( _files );
            // $( '.ajax-file-upload-container.latest-files' ).remove();
        },
        onSubmit: function () {
            if ( $( '#media-modal .this-container' ).outerHeight() > $( '#media-modal' ).outerHeight() )
                $( '.media-modal-bg' ).height( $( '#media-modal .this-container' ).outerHeight() );
        }
    } );
}

function open_media_modal()
{
    do_file_uploader();
    $( 'body' ).addClass( 'overflow-hidden' );
    $( '#media-modal, #media-modal .this-loader' ).addClass( 'show-this' );
    if ( $( '#media-modal .this-container' ).outerHeight() > $( '#media-modal' ).outerHeight() )
        $( '.media-modal-bg' ).height( $( '#media-modal .this-container' ).outerHeight() );
    $( '#media-modal' ).scrollTop( 0 );
    update_last_files_list( function () {
        setTimeout( function () {
            $( '#media-modal .this-loader' ).removeClass( 'show-this' );
            $( '#media-modal .this-container' ).addClass( 'show-this' );
        }, 350 );
    } );
}

function close_media_modal( file )
{
    $( '#media-modal' ).removeClass( 'show-this' );
    close_image_size_select();
    setTimeout( function () {
        $( '#media-modal .this-container' ).removeClass( 'show-this' );
        $( 'body' ).removeClass( 'overflow-hidden' );
    }, 350 );
}

function update_last_files_list( onfinished = null )
{
    if ( onfinished == null )
        onfinished = function () {
        };
    if ( _last_files_upadated_type == _upload_type )
    {
        onfinished();
        return false;
    }
    var _container = $( '.ajax-file-upload-container.latest-files' );
    _container.hide();
    $.post( _file_uploader_data.refresh_url, {
        _token: _file_uploader_data.token,
        _method: 'PATCH',
        _media_type: _upload_type
    }, function ( resp, textStatus, jqXHR ) {
        $( resp ).insertAfter( '.ajax-file-upload-container.latest-files' );
        _container.remove();
        if ( $( '#media-modal .this-container' ).outerHeight() > $( '#media-modal' ).outerHeight() )
            $( '.media-modal-bg' ).height( $( '#media-modal .this-container' ).outerHeight() );
        _last_files_upadated_type = _upload_type;
        onfinished();
    } );
}

function fixed_upload_area()
{
    var _scrollTop = $( '#media-modal' ).scrollTop();
    var _half_height_select_size = ($( window ).height() / 2);
    $( '#media-modal .close-modal' ).css( 'top', (_scrollTop - 2) + 'px' );
    $( '#fileuploader' ).css( 'top', (_scrollTop + 50) + 'px' );
    $( '#media-modal .image-size-select' ).css( 'top', (_scrollTop + _half_height_select_size) + 'px' );
}

function open_image_size_select()
{
    $( '#media-modal .image-size-select' ).addClass( 'show-this' );
}

function close_image_size_select()
{
    $( '#media-modal .image-size-select' ).removeClass( 'show-this' );
}
