@extends('layouts.admin')
@section('title', lang( "Edit Media" ))

@section('styles')
    <link rel="stylesheet" href="{{ admin_assets_url() }}/app-assets/css-rtl/pages/users.css">
@endsection

@section( 'content' )
    <div id="content">
        <section id="widget-grid" class="">
            <div class="row" id="user-profile">
                <article class="col-sm-12 col-md-5 sortable-grid ui-sortable">
                    <div class="col-12">
                        <div class="card profile-with-cover">
                            <div class="card-img-top img-fluid bg-cover height-300" style="background: url('{{ in_array( $model->type, \App\Models\Media::ALLOWED_IMAGE_MIME_TYPES ) ? get_media_url($model,'medium') : admin_assets_url('app-assets/images/carousel/22.jpg') }}') 50%;">
                                @if ( $model->type == 'video/mp4' )
                                    <video class="w-100 h-100 bg-black" src="{{ get_media_url($model,'medium') }}" controls></video>
                                @endif
                            </div>
                            <div class="media profil-cover-details w-100">
                            </div>
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
    </script>
@stop
