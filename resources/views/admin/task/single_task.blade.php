<tr>
    <td>{{ $model->title }}</td>
    <td>
        <div class="badge badge-{{ $model->getPriorities()[$model->priority]['color'] }}">
            {{ $model->getPriorities()[$model->priority]['name'] }}
        </div>
    </td>
    <td>
        <a href='{{ route( 'admin.task.edit', [ $model->id ] ) }}' style="padding: 3px;" class="mt-1 btn btn-xs btn-primary data-table-row-tool mr-1" data-toggle="tooltip" data-placement="top" data-original-title="{{ lang('Edit') }}">
            <i class="la la-pencil"></i>
        </a>
        <form action='{{ route( 'admin.task.destroy', [ $model->id ] ) }}' method='post' class='are-you-sure data-table-tools-form'>
            {{ method_field('delete') }}
            @csrf
            <button type='submit' style="padding: 3px;" class="mt-1 btn btn-xs btn-danger data-table-row-tool" data-toggle="tooltip" data-placement="top" data-original-title="{{ lang('Remove') }}">
                <i class="la la-close"></i>
            </button>
        </form>
        <button data-toggle="modal" data-target="#moveTaskModal{{ $model->id }}" style="padding: 3px;" class="mt-1 btn btn-xs btn-info data-table-row-tool" data-placement="top" data-original-title="{{ lang('Remove') }}">
            <i class="la la-paperclip"></i>
        </button>
        <div class="modal fade text-left" id="moveTaskModal{{ $model->id }}" tabindex="-1" role="dialog" aria-labelledby="moveTaskModal"
             aria-hidden="true">
            <div class="modal-dialog modal-xs" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="moveTaskModal">{{ lang( 'Move task to' ) }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.task.move', [ $model->id ]) }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <select name="status" class="select2 form-control">
                                @foreach( \App\Models\Task::getStatuses() as $sk => $status )
                                    <option value="{{ $sk }}" {{ $sk == $model->status ? 'selected' : '' }}>{{ $status['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">{{ lang( 'Close' ) }}</button>
                            <button type="submit" class="btn btn-outline-primary">{{ lang( 'Save' ) }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </td>
</tr>
