{!! widget_before( [
    'title' => lang( 'Todo' ),
    'color' => 'warning',
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
    @include('admin.task.table', [ '_status' => \App\Models\Task::STATUS_TODO ])
</table>
{!! widget_after() !!}
