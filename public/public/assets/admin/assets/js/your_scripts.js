$( document ).ready( function () {
//    are you sure button
    $( 'body' ).on( 'click submit', '.are-you-sure', function ( e ) {
        var _this = $( this );
        if ( _this.is( 'form' ) )
        {
            if ( !_this.hasClass( 'pressed' ) )
            {
                e.preventDefault();
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
                            closeModal: false
                        }
                    }
                } ).then( function ( isConfirm ) {
                    if ( isConfirm )
                    {
                        _this.submit();
                    }else
                    {
                        _this.removeClass( 'pressed' );
                    }
                } );
                _this.addClass( 'pressed' );
            }
        }
        if ( _this.is( 'a' ) )
        {
            e.preventDefault();
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
                        closeModal: false
                    }
                }
            } ).then( function ( isConfirm ) {
                if ( isConfirm )
                {
                    window.location.href = _this.attr( 'href' );
                }
            } );
        }
    } )

    if ( $( '.select2' ).length )
    {
        $( '.select2' ).each( function () {
            $( this ).select2();
        } )
    }

    $( ".datatable, .dataTable, .data-table" ).each(function () {
        $(this).DataTable( {
            autoWidth: false,
            paging: $(this).hasClass('paging') ? true : false,
            pageLength: ( $(this).hasClass('paging') && $(this).data('paging') ) ? $(this).data('paging') : 10,
            searching: false,
            lengthChange: false,
            info: false,
            language: {
                aria: {
                    paginate: {
                        next: lang.next,
                        previous: lang.prev,
                        first: lang.first,
                        last: lang.last,
                    }
                }
            },
            order: []
        } );
    })

    $('.checkbox, .radio').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    });
    $('.icheckbox_flat-green, .iradio_flat-green').each(function () {
        if ( $(this).find('input:checked').length )
            $(this).addClass('checked');
    })

    var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));

    elems.forEach(function(html) {
        var switchery = new Switchery(html, {
            color: 'rgb(59, 175, 218)',
            size: 'small'
        });
    });
} )

// function remove_nav_if_empty( el )
// {
//     var parent = el.parent().parent();
//     if( parent.is('li') )
//     {
//         parent = parent.parent().parent();
//         if ( parent.is('li') )
//         {
//             remove_nav_if_empty( parent )
//         }
//         else
//         {
//
//         }
//     }
// }
