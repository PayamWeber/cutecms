@extends('layouts.admin')
@section('title', lang('Edit Category'))

@section( 'content' )
    <div id="content">
        <section id="widget-grid" class="">
            <div class="row">
                <article class="col-sm-12 col-md-6 col-lg-6 sortable-grid ui-sortable">
                    @include('admin.post.categories.list')
                </article>
                <article class="col-sm-12 col-md-6 col-lg-6 sortable-grid ui-sortable">
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
