@extends('layouts.admin')
@section('title', lang('Pages'))

@section( 'content' )
    <div id="content">
        <section id="widget-grid" class="">
            <div class="row">
                <article class="col-sm-12 sortable-grid ui-sortable">
                    @include('admin.page.list')
                </article>
            </div>
        </section>
    </div>
@stop

@section('scripts')
    <script>
    </script>
@stop
