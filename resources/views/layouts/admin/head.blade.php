<!DOCTYPE html>
<html class="loading" lang="fa_IR" data-textdirection="rtl">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="Modern admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities with bitcoin dashboard.">
        <meta name="keywords" content="admin template, modern admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
        <meta name="author" content="PIXINVENT">
        <title>@yield('title') - {{ get_option('site_title') }}</title>
        <link rel="apple-touch-icon" href="{{ admin_assets_url() }}/app-assets/images/ico/apple-icon-120.png">
        <link rel="shortcut icon" type="image/x-icon" href="{{ admin_assets_url() }}/app-assets/images/ico/favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
              rel="stylesheet">
        <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css"
              rel="stylesheet">
    <?php
    $rtl_append = '';
    global $lang;
    if ( $lang == 'fa_IR' )
        $rtl_append = '-rtl';
    ?>
    <!-- BEGIN VENDOR CSS-->
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/css{{ $rtl_append }}/vendors.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/vendors/css/weather-icons/climacons.min.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/fonts/meteocons/style.css">
        <!-- END VENDOR CSS-->
        <!-- BEGIN MODERN CSS-->
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/css{{ $rtl_append }}/app.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/css{{ $rtl_append }}/custom{{ $rtl_append }}.css">
        <!-- END MODERN CSS-->
        <!-- BEGIN Page Level CSS-->
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/css{{ $rtl_append }}/core/menu/menu-types/vertical-menu-modern.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/css{{ $rtl_append }}/core/colors/palette-gradient.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/fonts/simple-line-icons/style.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/css{{ $rtl_append }}/core/colors/palette-gradient.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/vendors/css/forms/selects/select2.min.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/vendors/css/forms/icheck/icheck.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/vendors/css/forms/icheck/custom.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/css/plugins/forms/checkboxes-radios.css">
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/app-assets/vendors/css/forms/toggle/switchery.min.css">
        <!-- END Page Level CSS-->
        <!-- BEGIN Custom CSS-->
        <link rel="stylesheet" type="text/css" href="{{ admin_assets_url() }}/assets/css/style{{ $rtl_append }}.css">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ admin_assets_url() }}/app-assets/fonts/IRANSans/css/fontiran.css">
        <link rel="stylesheet" type="text/css" media="screen" href="{{ admin_assets_url() }}/assets/css/your_style.css">
        @include( 'layouts.admin.media.styles')
        @if( $rtl_append )
            <link rel="stylesheet" type="text/css" media="screen" href="{{ admin_assets_url() }}/assets/css/your_style{{ $rtl_append }}.css">
        @endif
    <!-- END Custom CSS-->
        @yield('styles')
    </head>
    <body class="vertical-layout vertical-menu-modern 2-columns menu-expanded fixed-navbar {{ $GLOBALS['lang'] }} @yield('body_class')"
          data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
