@extends('layouts.admin')
@section('title', lang( "Edit User" ))

@section('styles')
    <link rel="stylesheet" href="{{ admin_assets_url() }}/app-assets/css-rtl/pages/users.css">
@endsection

@section( 'content' )
    <div id="content">
        <section id="widget-grid" class="">
            <div class="row" id="user-profile">
                <div class="col-12">
                    {!! print_alert( lang( 'If you don\'t want to change password, just leave it empty' ) ) !!}
                </div>
                <article class="col-sm-12 col-md-5 sortable-grid ui-sortable">
                    <div class="col-12">
                        <div class="card profile-with-cover">
                            <div class="card-img-top img-fluid bg-cover height-300" style="background: url('{{ get_media_url($model->avatar_id,'medium') }}') 50%;"></div>
                            <div class="media profil-cover-details w-100">
                                <div class="media-left pl-2 pt-2">
                                    <a href="#" class="profile-image">
                                        <img src="{{ get_media_url($model->avatar_id,'thumbnail') }}"
                                             data-default-src="{{ admin_assets_url() }}/app-assets/images/portrait/small/avatar-s-8.png"
                                             class="rounded-circle avatar-image img-border height-100" alt="Card image">
                                    </a>
                                </div>
                                <div class="media-body pt-3 px-2">
                                    <div class="row">
                                        <div class="col">
                                            <h3 class="card-title profile-name"></h3>
                                        </div>
                                        <div class="col text-right">
                                            <div id='avatar_upload' class='media-upload-buttons' data-upload-type='avatar'>
                                                <div class="btn-group d-none d-md-block float-right ml-2 this-buttons" role="group" aria-label="Basic example">
                                                    <button type="button" class="btn btn-danger delete-current-media {{ $model->avatar_id ? '' : 'hidden' }}">
                                                        <i class="la la-close"></i></button>
                                                    <button type="button" class="btn btn-success open-media-modal-button">
                                                        <i class="la la-cog"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <nav class="navbar navbar-light navbar-profile align-self-end">
                                <button class="navbar-toggler d-sm-none" type="button" data-toggle="collapse" aria-expanded="false" aria-label="Toggle navigation"></button>
                                <nav class="navbar navbar-expand-lg">
                                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                        <ul class="navbar-nav mr-auto">
                                            <li class="nav-item active">
                                                <a class="nav-link" href="#"><i class="la la-line-chart"></i> Timeline
                                                    <span class="sr-only">(current)</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#"><i class="la la-user"></i> Profile</a>
                                            </li>
                                        </ul>
                                    </div>
                                </nav>
                            </nav>
                        </div>
                    </div>
                </article>
                <article class="col-sm-12 col-md-7 sortable-grid ui-sortable">
                    {!! $form !!}
                </article>
            </div>
        </section>
    </div>
@stop

@section('scripts')
    <script>
        $( '[name="nick_name"]' ).keyup( function () {
            $( '.profile-name' ).text( $( this ).val() );
        } )
    </script>
@stop
