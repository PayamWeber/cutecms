<thead>
    <tr>
        <th>{{ lang('Title') }}</th>
        <th>{{ lang('Priority') }}</th>
        <th></th>
    </tr>
</thead>
<tbody>
    @if ( $models )
        @foreach( $models->where('status', $_status)->all() as $model )
            @include('admin.task.single_task')
        @endforeach
    @endif
</tbody>
<tfoot>
    <tr>
        <th>{{ lang('Title') }}</th>
        <th>{{ lang('Priority') }}</th>
        <th></th>
    </tr>
</tfoot>
