@extends('layouts.admin')
@section('title', 'ویرایش دسترسی')

@section( 'content' )
    <div id="content">
        <section id="widget-grid" class="">
            <div class="row">
                <article class="col-sm-12 col-md-5 col-lg-5 sortable-grid ui-sortable">
                    {!! $form !!}
                </article>
                <article class="col-sm-12 col-md-7 col-lg-7 sortable-grid ui-sortable">
                    @include('admin.programmer.capabilities.list')
                </article>
            </div>
        </section>
    </div>
@stop

@section('scripts')
    <script>
    </script>
@stop
