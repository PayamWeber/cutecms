@include('layouts.admin.js_translation')
<!-- BEGIN VENDOR JS-->
<script src="{{ admin_assets_url() }}/app-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ admin_assets_url() }}/app-assets/vendors/js/charts/raphael-min.js" type="text/javascript"></script>
<!-- END PAGE VENDOR JS-->
<!-- BEGIN MODERN JS-->
<script src="{{ admin_assets_url() }}/app-assets/js/core/app-menu.js" type="text/javascript"></script>
<script src="{{ admin_assets_url() }}/app-assets/js/core/app.js" type="text/javascript"></script>
<script src="{{ admin_assets_url() }}/app-assets/js/scripts/customizer.js" type="text/javascript"></script>
<script src="{{ admin_assets_url() }}/app-assets/vendors/js/forms/select/select2.full.min.js" type="text/javascript"></script>
<script src="{{ admin_assets_url() }}/app-assets/vendors/js/tables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="{{ admin_assets_url() }}/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js" type="text/javascript"></script>
<script src="{{ admin_assets_url() }}/app-assets/vendors/js/extensions/sweetalert.min.js" type="text/javascript"></script>
<script src="{{ admin_assets_url() }}/app-assets/vendors/js/forms/icheck/icheck.min.js" type="text/javascript"></script>
<script src="{{ admin_assets_url() }}/app-assets/vendors/js/forms/toggle/switchery.min.js" type="text/javascript"></script>
@include( 'layouts.admin.media.scripts')
<!-- END MODERN JS-->
<!-- BEGIN PAGE LEVEL JS-->
<script src="{{ admin_assets_url() }}/assets/js/your_scripts.js" type="text/javascript"></script>
<!-- END PAGE LEVEL JS-->
@yield( 'scripts' )
