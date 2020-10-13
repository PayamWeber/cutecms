{!! widget_before( [
    'title' => lang( 'Users' ),
    'color' => 'white',
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
            <th>{{ lang('Name') }}</th>
            <th>{{ lang('Email') }}</th>
            <th>{{ lang('Role') }}</th>
            <th>{{ lang('Operation') }}</th>
        </tr>
    </thead>
    <tbody>
        @if ( $models )
            @foreach( $models as $model )
                <tr>
                    <td>{{ $counter-- }}</td>
                    <td>
                        <div class="media">
                            <div class="media-left pr-1">
                                <span class="avatar avatar-sm avatar-online rounded-circle">
                                    <img src="{{ get_media_url( $model->avatar_id, 'thumbnail' ) }}" alt="avatar">
                                    <i></i></span>
                            </div>
                            <div class="media-body media-middle">
                                <a href="{{ route( 'admin.role.edit', [ 'role' => $model->id ] ) }}" class="media-heading">{{ $model->name }}</a>
                            </div>
                        </div>
                    </td>
                    <td>{{ $model->email }}</td>
                    <td>{{ $model->role->title }}</td>
                    <td>
                        <a href='{{ route( 'admin.user.edit', [ 'user' => $model->id ] ) }}' class="btn btn-xs btn-primary data-table-row-tool mr-1" data-toggle="tooltip" data-placement="top" data-original-title="{{ lang('Edit') }}">
                            <i class="la la-pencil"></i>
                        </a>
                        <form action='{{ route( 'admin.user.destroy', [ 'user' => $model->id ] ) }}' method='post' class='are-you-sure data-table-tools-form'>
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
            <th>{{ lang('Name') }}</th>
            <th>{{ lang('Email') }}</th>
            <th>{{ lang('Role') }}</th>
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
