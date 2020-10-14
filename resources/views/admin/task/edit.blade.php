
@extends('layouts.admin')
@section('title', lang('Edit Task'))

@section( 'content' )
    <div id="content">
        <section id="widget-grid" class="">
            <div class="row">
                <article class="col-sm-12 col-md-2 col-lg-2 sortable-grid ui-sortable">
                </article>
                <article class="col-sm-12 col-md-8 col-lg-8 sortable-grid ui-sortable">
                    {!! $form !!}
                </article>
                <article class="col-sm-12 col-md-2 col-lg-2 sortable-grid ui-sortable">
                </article>
            </div>
            <div class="row">
                <article class="col-sm-12 col-md-4 col-lg-4 sortable-grid ui-sortable">
                    @include('admin.task.todo')
                </article>
                <article class="col-sm-12 col-md-4 col-lg-4 sortable-grid ui-sortable">
                    @include('admin.task.doing')
                </article>
                <article class="col-sm-12 col-md-4 col-lg-4 sortable-grid ui-sortable">
                    @include('admin.task.done')
                </article>
            </div>
        </section>
    </div>
@stop

@section('scripts')
    <script>
    </script>
    <style>
        .table th {
            padding: 0.75rem 0.2rem;
            padding-right: 2rem;
        }
        .table td {
            padding: 0.3rem ;
        }
        .table td:last-child {
            text-align: center;
        }
        .table td .btn {
            margin-right: 5px !important;
        }
    </style>
@stop
