{!! widget_before( [
    'title' => lang( 'String Translation' ),
    'color' => 'white',
] ) !!}
<form action="" method="get" id="quick-filter-form">
    <div class="row">
        <div class="col-md-6">
            <div id="invoices-list_filter" class="dataTables_filter pull-left">
                <label>
                    <input name="search" type="search" class="form-control form-control-sm" placeholder="{{ lang('Search') }}" aria-controls="invoices-list" value="{{ \Request::get('search') }}">
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div id="invoices-list_filter" class="dataTables_filter pull-right">
                <select name="lang" id="change-list-lang" class="select2 form-control">
                    <?php global $langs; global $lang; ?>
                    @foreach( $langs as $l )
                        <option value="{{ $l }}" {{ ( \Request::get('lang', $lang) == $l ) ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</form>
<table id="dt_basic" class="table table-striped table-bordered sourced-data dataTable paging" data-paging="50" width="100%">
    <thead>
        <tr>
            <th>{{ lang('Row') }}</th>
            <th>{{ lang('Text Domain') }}</th>
            <th style="width: 35%;">{{ lang('String') }}</th>
            <th style="width: 35%;">{{ lang('Translation') }}</th>
            <th>{{ lang('Operation') }}</th>
        </tr>
    </thead>
    <tbody>
        @if ( $models )
            @foreach( $models as $text_domain => $_models )
                @if( $_models )
                    @foreach( $_models as $string => $translation )
                        <tr>
                            <td>{{ $counter-- }}</td>
                            <td>{{ $text_domain }}</td>
                            <td>{{ $string }}</td>
                            <td>
                                <input type="text" class="form-control" placeholder="{{ lang('Translation') }}" value="{{ $translation }}">
                            </td>
                            <td>
                                <a class="btn btn-xs btn-success data-table-row-tool udpate-string" data-toggle="tooltip" data-placement="top" data-text-domain="{{ $text_domain }}" data-string="{{ $string }}" data-original-title="{{ lang('Update') }}">
                                    <i class="la la-save"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        @endif
    </tbody>
    <tfoot>
        <tr>
            <th>{{ lang('Row') }}</th>
            <th>{{ lang('Text Domain') }}</th>
            <th>{{ lang('String') }}</th>
            <th>{{ lang('Translation') }}</th>
            <th>{{ lang('Operation') }}</th>
        </tr>
    </tfoot>
</table>
<div class="dt-toolbar-footer col-xs-12">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="dataTables_paginate paging_simple_numbers" id="dt_basic_paginate">
                {!! '' !!}
            </div>
        </div>
    </div>
</div>
{!! widget_after() !!}
