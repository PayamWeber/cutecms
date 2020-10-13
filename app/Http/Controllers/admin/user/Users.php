<?php

namespace App\Http\Controllers\admin\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Session;

class Users extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = $this->make_data_list();
        $form = $this->make_insert_form();
        return view( 'admin/programmer/users/manage_users', compact( [ 'data', 'form' ] ) );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store( Request $request )
    {
//        validate request
        $request->validate( [
            'title' => 'required',
            'name' => 'required',
            'controller' => 'required',
        ], [
            'title.required' => 'لطفا عنوان را وارد نمایید',
            'name.required' => 'لطفا نام دسترسی را وارد نمایید',
            'controller.required' => 'لطفا کنترلر را وارد نمایید',
        ] );
        $cap             = new Capability;
        $cap->title      = $request->title;
        $cap->name       = $request->name;
        $cap->is_route   = ( $request->is_route == 1 ) ? $request->is_route : '0';
        $cap->parent     = $request->parent;
        $cap->controller = ( strpos( $request->controller, ',' ) !== false ) ? json_encode( explode( ',', $request->controller ) ) : $request->controller;
        $cap->save();
        Session::flash( 'pmw_message', 'دسترسی با موفقیت ثبت شد' );
        return back();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit( Capability $capability )
    {
        $caps_table = $this->make_data_list();
        $form       = $this->make_edit_form( $capability );
        return view( 'admin/programmer/capabilities/edit_cap', compact( [ 'form', 'caps_table' ] ) );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update( Request $request, Capability $capability )
    {
//        validate request
        $request->validate( [
            'title' => 'required',
            'name' => 'required',
            'controller' => 'required',
        ], [
            'title.required' => 'لطفا عنوان را وارد نمایید',
            'name.required' => 'لطفا نام دسترسی را وارد نمایید',
            'controller.required' => 'لطفا کنترلر را وارد نمایید',
        ] );
        $cap             = $capability;
        $cap->title      = $request->title;
        $cap->name       = $request->name;
        $cap->is_route   = ( $request->is_route == 1 ) ? $request->is_route : '0';
        $cap->parent     = $request->parent;
        $cap->controller = ( strpos( $request->controller, ',' ) !== false ) ? json_encode( explode( ',', $request->controller ) ) : $request->controller;
        $cap->save();
        Session::flash( 'pmw_message', 'دسترسی با موفقیت ذخیره شد' );
        return back();
    }

    public function destroy( Capability $capability )
    {
        $capability->delete();
        Session::flash( 'pmw_message', 'دسترسی با موفقیت حذف شد' );
        return back();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function manage_capabilities_edit()
    {
        $form       = $this->make_insert_form();
        $caps_table = $this->make_data_list();
        return view( 'admin/programmer/manage_caps', compact( [ 'form', 'caps_table' ] ) );
    }

    /**
     * @return mixed
     */
    public function make_insert_form()
    {
        $caps    = CapabilityCat::orderBy( 'title', 'ASC' )->get();
        $parents = [];

        $parents[ '0' ] = 'انتخاب کنید';
        foreach ( $caps as $cap ) $parents[ $cap->id ] = $cap->title;

        return make_smart_ui_form(
            [ // args
              'options' => [
                  'method' => 'post',
                  'action' => url_capabilities( '' ),
              ],
              'title' => 'افزودن یک دسترسی',
              'submit_text' => 'انتشار',
            ], [ // fields
                 'title' => array(
                     'type' => 'input',
                     'col' => 12,
                     'properties' => array(
                         'label' => 'عنوان',
                         'placeholder' => 'به فارسی',
                         'icon' => 'fa-bolt',
                         'icon_append' => false,
                         'value' => old( 'title' )
                     )
                 ),
                 'name' => array(
                     'type' => 'input',
                     'col' => 12,
                     'properties' => array(
                         'label' => 'نام',
                         'placeholder' => 'به انگلیسی',
                         'icon' => 'fa-bolt',
                         'icon_append' => false,
                         'value' => old( 'name' )
                     )
                 ),
                 'controller' => array(
                     'type' => 'input',
                     'col' => 12,
                     'properties' => array(
                         'label' => 'کنترلر',
                         'placeholder' => 'App\Http\Controllers\\',
                         'icon' => 'fa-bolt',
                         'icon_append' => true,
                         'attr' => [
                             'style="direction:ltr;text-align:left;"'
                         ],
                         'value' => old( 'controller' ) ?? 'App\Http\Controllers\\'
                     ),
                 ),
                 'parent' => array(
                     'type' => 'select',
                     'col' => 12,
                     'properties' => array(
                         'data' => $parents,
                         'label' => 'مادر',
                         'selected' => 0
                     ),
                 ),
                 'is_main' => array(
                     'type' => 'checkbox',
                     'col' => 12,
                     'properties' => array(
                         'items' => [
                             [
                                 'name' => 'is_route',
                                 'checked' => false,
                                 'value' => '1',
                                 'label' => 'قرار دادن به عنوان دسترسی مسیری',
                                 'id' => 'is_main',
                                 'disabled' => false
                             ]
                         ]
                     ),
                 ),
        ], [ // fieldset
             0 => [ 'title', 'name', 'controller', 'parent', 'is_main' ],
        ] );
    }

    /**
     * @param Capability $capability
     *
     * @return mixed
     */
    public function make_edit_form( $capability )
    {
        $caps    = CapabilityCat::orderBy( 'title', 'ASC' )->get();
        $parents = [];
        foreach ( $caps as $cap ) $parents[ $cap->id ] = $cap->title;

        return make_smart_ui_form(
            [ // args
              'options' => [
                  'method' => 'post',
                  'action' => url_capabilities( '/' . $capability->id ),
              ],
              'title' => $capability->title,
              'submit_text' => 'بروزرسانی',
            ], [ // fields
                 '_method' => [
                     'type' => 'hidden',
                     'properties' => array(
                         'value' => 'patch'
                     )
                 ],
                 'title' => array(
                     'type' => 'input',
                     'col' => 12,
                     'properties' => array(
                         'label' => 'عنوان',
                         'placeholder' => 'به فارسی',
                         'icon' => 'fa-bolt',
                         'icon_append' => false,
                         'value' => $capability->title
                     )
                 ),
                 'name' => array(
                     'type' => 'input',
                     'col' => 12,
                     'properties' => array(
                         'label' => 'نام',
                         'placeholder' => 'به انگلیسی',
                         'icon' => 'fa-bolt',
                         'icon_append' => false,
                         'value' => $capability->name
                     )
                 ),
                 'controller' => array(
                     'type' => 'input',
                     'col' => 12,
                     'properties' => array(
                         'label' => 'کنترلر',
                         'placeholder' => 'App\Http\Controllers\\',
                         'icon' => 'fa-bolt',
                         'icon_append' => true,
                         'attr' => [
                             'style="direction:ltr;text-align:left;"'
                         ],
                         'value' => ( strpos( $capability->controller, '[' ) !== false ) ? implode( ',', json_decode( $capability->controller ) ) : $capability->controller
                     ),
                 ),
                 'parent' => array(
                     'type' => 'select',
                     'col' => 12,
                     'properties' => array(
                         'data' => $parents,
                         'label' => 'مادر',
                         'selected' => $capability->parent
                     ),
                 ),
                 'is_main' => array(
                     'type' => 'checkbox',
                     'col' => 12,
                     'properties' => array(
                         'items' => [
                             [
                                 'name' => 'is_route',
                                 'checked' => ( $capability->is_route == 1 ) ? true : false,
                                 'value' => '1',
                                 'label' => 'قرار دادن به عنوان دسترسی مسیری',
                                 'id' => 'is_main',
                                 'disabled' => false
                             ]
                         ]
                     ),
                 ),
        ], [ // fieldset
             0 => [ '_method', 'title', 'name', 'controller', 'parent', 'is_main' ],
        ] );
    }

    /**
     * @return string
     */
    public function make_data_list()
    {
        $row_per_page = 15;
        $search       = $_GET[ 'search' ] ?? '';
        $caps         = Capability::orderBy( 'id', 'desc' )->paginate( $row_per_page );

        if ( $search )
            $caps = Capability::where( 'name', 'LIKE', "%$search%" )
                ->orWhere( 'title', 'LIKE', "%$search%" )
                ->orWhere( 'controller', 'LIKE', "%$search%" )->orderBy( 'id', 'desc' )->paginate( $row_per_page );

        $_GET[ 'counter' ] = ( $caps->count() * $caps->lastPage() ) - ( $row_per_page * ( $caps->currentPage() - 1 ) );

        return make_data_table( [
            'title' => $_GET[ 'search' ] ?? 'دسترسی های فعلی',
            'top_button' => [
                'title' => 'افزودن دسترسی',
                'url' => url_capabilities( '' )
            ],
            'rowSet' => [
                'id' => 'ردیف',
                'title' => 'عنوان',
                'name' => 'نام',
                'tools' => 'عملیات',
            ],
            'rows' => $caps,
            'customRows' => [
                'title' => function ( $row_key, $row_value, $counter ) {
                    $category = CapabilityCat::findOrFail( $row_value->parent );
                    return $category->title . ' (' . $row_value->title . ')';
                },
                'tools' => function ( $row_key, $row_value, $counter ) {
                    $delete_action = url_capabilities( '/' . $row_value->id );
                    $edit_action   = url_capabilities( '/' . $row_value->id . '/edit' );
                    $html          = "<a href='$edit_action' class=\"btn btn-xs btn-primary data-table-row-tool\" data-original-title='ویرایش' title=\"\"><i class=\"fa fa-pencil\"></i></a>";
                    $html          .= "<form action='$delete_action' method='post' class='are-you-sure data-table-tools-form'>";
                    $html          .= method_field( 'delete' );
                    $html          .= csrf_field();
                    $html          .= "<button type='submit' class=\"btn btn-xs btn-danger data-table-row-tool\" data-original-title='حذف' title=\"\"><i class=\"fa fa-close\"></i></button>";
                    $html          .= "</form>";
                    return $html;
                },
                'id' => function () {
                    $html = $_GET[ 'counter' ];
                    $_GET[ 'counter' ]--;
                    return $html;
                }
            ],
            'pagination' => [
                'links' => $caps->links(),
                'rows_per_page' => $row_per_page
            ],
        ] );
    }
}
