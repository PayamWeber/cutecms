{!! widget_before( [
    'title' => lang( 'Categories' ),
    'color' => 'success',
] ) !!}
<div class="row">
    <div class="col-sm-12 col-md-12">
        <div id="invoices-list_filter" class="dataTables_filter">
            <form action="" method="get">
                <label>
                    <input name="search" type="search" class="form-control form-control-sm" placeholder="{{ lang('Search') }}" aria-controls="invoices-list">
                </label>
            </form>
        </div>
    </div>
</div>
<table id="dt_basic" class="table table-striped table-bordered sourced-data dataTable" width="100%">
    <thead>
        <tr>
            <th>{{ lang('Row') }}</th>
            <th>{{ lang('Title') }}</th>
            <th>{{ lang('Slug') }}</th>
            <th>{{ lang('Operation') }}</th>
        </tr>
    </thead>
    <tbody>
        @if ( $models )
            @foreach( $models as $model )
                <tr>
                    <td>{{ $counter-- }}</td>
                    <td>{{ $model->title }}</td>
                    <td>{{ $model->slug }}</td>
                    <td>
                        <a href='{{ route( 'admin.post_category.edit', [ $model->id ] ) }}' class="btn btn-xs btn-primary data-table-row-tool mr-1" data-toggle="tooltip" data-placement="top" data-original-title="{{ lang('Edit') }}">
                            <i class="la la-pencil"></i>
                        </a>
                        <form action='{{ route( 'admin.post_category.destroy', [ $model->id ] ) }}' method='post' class='are-you-sure data-table-tools-form'>
                            {{ method_field('delete') }}
                            {{ csrf_field() }}
                            <button type='submit' class="btn btn-xs btn-danger data-table-row-tool" data-toggle="tooltip" data-placement="top" data-original-title="{{ lang('Remove') }}">
                                <i class="la la-close"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
    <tfoot>
        <tr>
            <th>{{ lang('Row') }}</th>
            <th>{{ lang('Title') }}</th>
            <th>{{ lang('Slug') }}</th>
            <th>{{ lang('Operation') }}</th>
        </tr>
    </tfoot>
</table>
<div class="dt-toolbar-footer col-xs-12">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="dataTables_paginate paging_simple_numbers" id="dt_basic_paginate">
                {!! $models->render() !!}
            </div>
        </div>
    </div>
</div>
{!! widget_after() !!}
