<?php

namespace App\Http\Controllers\Admin\Media;

use App\Forms\MediaForm;
use App\Helpers\AlertHelper;
use App\Http\Middleware\RedirectIfAuthenticated;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use App\Models\Media;

class MediaController extends Controller
{
    protected $form;
    protected $validation;
    public    $data;

    public function __construct( Request $request )
    {
        $this->form = new MediaForm();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index( Request $request )
    {
        $this->data[ 'rows_per_page' ] = 25;
        $this->data[ 'models' ]        = $this->filter( $request );
        $this->data[ 'counter' ]       = $this->data[ 'models' ]->total() - ( $this->data[ 'rows_per_page' ] * ( $this->data[ 'models' ]->currentPage() - 1 ) );
        return view( 'admin.media.index', $this->data );
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function store( Request $request )
    {
        $files = $request->files->all();
        if ( $files ) {
            $errors = [];
            foreach ( $files as $file ) {
                $path      = date( 'Y/m' ) . '/';
                $file_name = $file->getClientOriginalName();
                // file name without file format
                $clean_name = $file_title = Media::cleanFileName( Media::getOnlyName( $file_name ) );
                $just_name  = date( 'YmdHis' ) . '-' . $clean_name;

                $file_ext  = Media::getExt( $file_name );
                $file_mime = $file->getClientMimeType();
                $file_size = $file->getSize();
                $file_name = $just_name . '.' . $file_ext;

                $this->find_and_change_file_name( $file_name, $just_name, $file_ext );

                // check file size
                if ( $file_size > Media::getMaxFileSize() ) {
                    $errors[ $file->getClientOriginalName() ] = [
                        'text' => lang( 'File size is more than maximum allowed' ),
                    ];
                    continue;
                }

                // check file mime type
                if ( $request->_type == 'image' && is_user_can( [ 'media_store_image', 'media_store_file' ] ) ) {
                    if ( ! in_array( $file_mime, Media::ALLOWED_IMAGE_MIME_TYPES ) ) {
                        $errors[ $file->getClientOriginalName() ] = [
                            'text' => lang( 'Please insert an image file' ),
                        ];
                        continue;
                    }
                } else if( is_user_can( 'media_store_file' ) ) {
                    if ( ! in_array( $file_mime, Media::getAllowedMimeTypes() ) ) {
                        $errors[ $file->getClientOriginalName() ] = [
                            'text' => lang( 'File format is not allowed' ),
                        ];
                        continue;
                    }
                }

                if ( $errors ) {
                    return [
                        'type' => 'error',
                        'result' => $errors,
                    ];
                } else {
                    $media            = new Media();
                    $media->user_id   = Auth::id();
                    $media->title     = $file_title;
                    $media->alt       = '';
                    $media->path      = $path;
                    $media->name      = $just_name;
                    $media->ext       = $file_ext;
                    $media->full_path = $path . $file_name;
                    $media->type      = $file_mime;
                    $media->size      = $file_size;

                    if ( $media->save() ) {
                        $upload_path = public_path( Media::UPLOAD_PATH . date( 'Y/m' ) . '/' );

                        if ( ! is_dir( $upload_path ) ) {
                            mkdir( $upload_path, 0775, true );
                        }

                        $file->move( $upload_path, $file_name );

                        // resize image and make thumbnails
                        if ( in_array( $file_mime, Media::ALLOWED_CROPABLE_IMAGE_TYPES ) ) {
                            if ( Media::IMAGE_CROP_SIZES ) {
                                foreach ( Media::IMAGE_CROP_SIZES as $_crop_name => $_crop_size ) {
                                    if ( $_crop_name == 'full' )
                                        continue;

                                    $img = Image::make( public_path( Media::UPLOAD_PATH ) . $media->full_path );

                                    if ( $_crop_size[ 'w' ] == $_crop_size[ 'h' ] ) {
                                        $crop_size = $img->width();
                                        if ( $img->width() > $img->height() )
                                            $crop_size = $img->height();
                                        $img->crop( $crop_size, $crop_size );
                                    } else if ( $_crop_size[ 'w' ] && $_crop_size[ 'h' ] == null ) {
                                        if ( $img->height() > $img->width() ) {
                                            $_crop_size[ 'h' ] = $_crop_size[ 'w' ];
                                            $_crop_size[ 'w' ] = null;
                                        }
                                    }

                                    $img->resize( $_crop_size[ 'w' ], $_crop_size[ 'h' ], function ( $constraint ) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    } );
                                    $img->save( $upload_path . $just_name . '-' . $_crop_name . '.' . $file_ext );
                                }
                            }
                        }
                        $image_url = [];

                        if ( Media::IMAGE_CROP_SIZES && in_array( $file_mime, Media::ALLOWED_CROPABLE_IMAGE_TYPES ) ) {
                            foreach ( Media::IMAGE_CROP_SIZES as $CROP_NAME => $CROP_SIZE ) {
                                $image_url[ $CROP_NAME ] = $media->url( $CROP_NAME );
                            }
                        } else {
                            $image_url[ 'full' ] = $media->url();
                        }

                        return [
                            'type' => 'success',
                            'result' => [
                                $file->getClientOriginalName() => [
                                    'id' => $media->id,
                                    'name' => $media->name,
                                    'urls' => $image_url,
                                ],
                            ],
                        ];
                    } else {
                        return [
                            'type' => 'error',
                            'result' => [
                                $file->getClientOriginalName() => [
                                    'text' => lang( 'Database error' ),
                                ],
                            ],
                        ];
                    }
                }
            }
        }
    }

    /**
     * @param null $model
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit( $model = null )
    {
        $model                 = Media::findOrFail( $model );
        $this->data[ 'form' ]  = $this->form->edit( $model );
        $this->data[ 'model' ] = $model;
        return view( 'admin.media.edit', $this->data );
    }

    /**
     * @param Request $request
     * @param         $model
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update( Request $request, $model )
    {
        $model = Media::findOrFail( $model );
        $this->form->validation( $request, true );
        $errors = [];

        if ( $errors ) {
            foreach ( $errors as $error ) {
                AlertHelper::make( $error, 'danger' );
            }
            return back();
        } else {
            $model->name = $request->get( 'name', '' );
            if ( $model->save() ) {
                AlertHelper::make( lang( 'Operation successful' ) );
                return back();
            } else {
                AlertHelper::make( lang( 'there\'s a problem with saving data, Please try again' ), 'danger' );
                return back();
            }
        }
    }

    /**
     * this method delete a file
     *
     * @param Request $request
     * @param null    $id
     *
     * @return bool|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete( Request $request, $id = null )
    {
        $file = Media::filterByUser( is_user_can( 'media_destroy_other_file' ) ? 'all' : auth()->user() )
            ->find( $id ? $id : $request->file_id );
        if ( ! $file )
            return false;

        $file_path = $file->path;

        File::delete( public_path( Media::UPLOAD_PATH . '/' . $file->full_path ) );

        if ( Media::IMAGE_CROP_SIZES ) {
            foreach ( Media::IMAGE_CROP_SIZES as $_crop_name => $_crop_size ) {
                File::delete( public_path( Media::UPLOAD_PATH . $file_path . $file->name . '-' . $_crop_name . '.' . $file->ext ) );
            }
        }
        $file->delete();

        if ( $id ) {
            AlertHelper::make( Lang( 'Operation Successful' ) );
            return redirect( route( 'admin.media.index' ) );
        }
    }

    public function refresh( Request $request )
    {
        die( Media::get_latest_files( true, $request->_media_type ) );
    }

    private function find_and_change_file_name( &$file_name, &$just_name, $file_ext )
    {
        if ( Media::where( 'path', 'LIKE', '%' . $file_name )->first() ) {
            $just_name = $just_name . '-1';
            $file_name = $just_name . '.' . $file_ext;
            if ( Media::where( 'path', 'LIKE', '%' . $file_name )->first() ) {
                $this->find_and_change_file_name( $file_name, $just_name, $file_ext );
            }
        }
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    protected function filter( Request $request )
    {
        $models = Media::orderBy( 'created_at', 'desc' );

        if ( $request->get( 'search' ) )
            $models->where( 'name', 'LIKE', "%$request->search%" )
                ->orWhere( 'email', 'LIKE', "%$request->search%" );

        return $models->paginate( $this->data[ 'rows_per_page' ] );
    }
}
