<?php
$type        = $type ?? 'danger';
$message     = isset( $message ) ? $message : '';
$show_errors = ( isset( $show_errors ) && $show_errors ) ? TRUE : FALSE;
$icon_class  = 'check';
if ( $type == 'warning' || $type == 'danger' )
    $icon_class = 'exclamation';

$alert_messages = \App\Helpers\AlertHelper::get();
if ( ! $message && $alert_messages )
    $message = $alert_messages;
?>
@if( $message && is_string( $message ) )
    {!! print_alert( $message, $type) !!}
@endif
@if( $message && is_array( $message ) )
    @foreach( $message as $m )
        {!! print_alert( $m['message'], $m['type']) !!}
    @endforeach
@endif
@if( $errors->any() && $show_errors )
    @foreach( $errors->all() as $error )
        {!! print_alert( $error, 'danger') !!}
    @endforeach
@endif
