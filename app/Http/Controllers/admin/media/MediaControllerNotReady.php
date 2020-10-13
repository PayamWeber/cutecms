<?php

namespace App\Http\Controllers\Admin\Media;

use App\Models\MediaFolder;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use App\Models\Media;
use JalaliHelper;

class MediaController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index( Request $request, $folder_id = 0 )
    {
        $models = $this->filter( $request, false, $folder_id );
        return api_response( true, [
            'allowed_image_formats' => Media::ALLOWED_IMAGE_MIME_TYPES,
            'max_file_size' => Media::getMaxFileSize(),
            'files' => $models,
        ] );
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function globalIndex( Request $request )
    {
        $models = $this->filter( $request, true );
        return api_response( true, [
            'files' => $models,
        ] );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store( Request $request )
    {
        $request->validate( [
            'folder_id' => 'numeric',
            'file' => 'required',
        ] );
        $folder = $request->get( 'folder_id' );

        if ( $request->get( 'folder_id' ) )
        {
            $folder = $request->get( 'folder_id' ) ? MediaFolder::filterByUser()->find( $request->folder_id ) : false;
            if ( ! $folder )
            {
                return api_response( false, [
                    'folder_id' => [
                        'media.folder_not_found',
                    ],
                ] );
            }
        }

        $file = $request->file( 'file' );

        if ( $file )
        {
            $errors    = [];
            $path      = date( 'Y/m' ) . '/';
            $file_name = $file->getClientOriginalName();
            // file name without file format
            $clean_name = $file_title = Media::cleanFileName( Media::getOnlyName( $file_name ) );
            $just_name  = date( 'YmdHis' ) . '-' . $clean_name;

            $file_ext  = Media::getExt( $file_name );
            $file_mime = $file->getClientMimeType();
            $file_size = $file->getSize();
            $file_name = $just_name . '.' . $file_ext;

            $this->findAndChangeFileName( $file_name, $just_name, $file_ext );

            // check file size
            if ( $file_size > Media::getMaxFileSize() )
            {
                $errors[] = 'media.max_size';
            } // check file mime type
            else if ( $request->get( 'type' ) == 'image' && is_user_can( [ 'media_store_image', 'media_store_file' ] ) )
            {
                if ( ! in_array( $file_mime, Media::ALLOWED_IMAGE_MIME_TYPES ) )
                {
                    $errors[] = 'insert_only_image';
                }
            } else if ( $request->get( 'type' ) == 'file' && is_user_can( 'media_store_file' ) )
            {
                if ( ! in_array( $file_mime, Media::getAllowedMimeTypes() ) )
                {
                    $errors[] = 'file_format_fail';
                }
            }

            if ( $errors )
            {
                return api_response( false, $errors );
            } else
            {
                $media            = new Media();
                $media->user_id   = Auth::id();
                $media->folder_id = $folder ? $folder->id : 0;
                $media->title     = $file_title;
                $media->alt       = '';
                $media->path      = $path;
                $media->name      = $just_name;
                $media->ext       = $file_ext;
                $media->full_path = $path . $file_name;
                $media->type      = $file_mime;
                $media->size      = $file_size;

                if ( $media->save() )
                {
                    $upload_path = public_path( Media::UPLOAD_PATH . date( 'Y/m' ) . '/' );

                    if ( ! is_dir( $upload_path ) )
                    {
                        mkdir( $upload_path, 0775, true );
                    }

                    $file->move( $upload_path, $file_name );

                    // resize image and make thumbnails
                    if ( in_array( $file_mime, Media::ALLOWED_CROPABLE_IMAGE_TYPES ) )
                    {
                        if ( Media::IMAGE_CROP_SIZES )
                        {
                            foreach ( Media::IMAGE_CROP_SIZES as $_crop_name => $_crop_size )
                            {
                                if ( $_crop_name == 'full' )
                                    continue;

                                $img = Image::make( public_path( Media::UPLOAD_PATH ) . $media->full_path );

                                if ( $_crop_size[ 'w' ] == $_crop_size[ 'h' ] )
                                {
                                    $crop_size = $img->width();
                                    if ( $img->width() > $img->height() )
                                        $crop_size = $img->height();
                                    $img->crop( $crop_size, $crop_size );
                                } else if ( $_crop_size[ 'w' ] && $_crop_size[ 'h' ] == null )
                                {
                                    if ( $img->height() > $img->width() )
                                    {
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

                    return api_response( true, [
                        'id' => $media->id,
                        'title' => $media->title,
                        'alt' => $media->alt,
                        'size' => media_get_file_size( $media->size ),
                        'date' => JalaliHelper::jdate( 'H:i Y/n/d', strtotime( $media->created_at ), 'en' ),
                        'file_name' => $media->name . '.' . $media->ext,
                        'urls' => $image_url,
                    ] );
                } else
                {
                    return api_response( false, [
                        'media.database_fail',
                    ] );
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
        $model = Media::filterByUser( is_user_can( 'media_update_other_file' ) ? 'all' : auth()->user() )->find( $model );

        if ( ! $model )
        {
            return api_response( false, [ 'media.not_found' ] );
        }

        $url = [];

        if ( Media::IMAGE_CROP_SIZES && in_array( $model->type, Media::ALLOWED_CROPABLE_IMAGE_TYPES ) )
        {
            foreach ( Media::IMAGE_CROP_SIZES as $CROP_NAME => $CROP_SIZE )
            {
                $url[ $CROP_NAME ] = get_media_url( $model, $CROP_NAME );
            }
        } else
        {
            $url[ 'full' ] = get_media_url( $model );
        }

        return api_response( true, [
            'id' => $model->id,
            'title' => $model->title,
            'alt' => $model->alt,
            'urls' => $url,
        ] );
    }

    /**
     * @param Request $request
     * @param         $model
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update( $model, Request $request )
    {
        $request->validate( [
            'title' => [ 'required', 'max:255' ],
        ] );

        $model = Media::filterByUser( is_user_can( 'media_update_other_file' ) ? 'all' : auth()->user() )->find( $model );

        if ( ! $model )
        {
            return api_response( false, [ 'media.not_found' ] );
        }

        $errors = [];

        if ( $errors )
        {
            return api_response( false, $errors );
        } else
        {
            $model->title = Media::safeTitle( $request->get( 'title', '' ) );
            $model->alt   = Media::safeTitle( $request->get( 'alt', '' ) );
            if ( $model->save() )
            {
                return api_response( true );
            } else
            {
                return api_response( false, [ 'media.database_fail' ] );
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
    public function destroy( Request $request, $id )
    {
        $model = Media::filterByUser( is_user_can( 'media_destroy_other_file' ) ? 'all' : auth()->user() )->find( $id );

        //todo: remove this in production
        $model = Media::find( $id );

        if ( ! $model )
        {
            return api_response( false, [ 'media.not_found' ] );
        }

        $file_path = $model->path;

        File::delete( public_path( Media::UPLOAD_PATH . '/' . $model->full_path ) );

        if ( Media::IMAGE_CROP_SIZES ) {
            foreach ( Media::IMAGE_CROP_SIZES as $_crop_name => $_crop_size ) {
                File::delete( public_path( Media::UPLOAD_PATH . $file_path . $model->name . '-' . $_crop_name . '.' . $model->ext ) );
            }
        }
        $model->delete();

        return api_response( true );
    }

    /**
     * @param      $file_name
     * @param      $just_name
     * @param      $file_ext
     * @param bool $put_dash
     */
    private function findAndChangeFileName( &$file_name, &$just_name, $file_ext, $put_dash = false )
    {
        if ( $put_dash === true )
        {
            $just_name = $just_name . '-1';
            $file_name = $just_name . '.' . $file_ext;
        }
        if ( Media::where( 'path', 'LIKE', '%' . $file_name )->first() )
        {
            $this->findAndChangeFileName( $file_name, $just_name, $file_ext, true );
        }
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    protected function filter( Request $request, $show_public_folder = false, $folder_id = 0 )
    {
        $folder = $folder_id ? $folder_id : ( $show_public_folder ? get_option( 'public_folder_id' ) : 0 );

        /**
         * check if this is public folder then dont filter by user
         */
        if ( $folder && $folder == get_option( 'public_folder_id' ) )
        {
            $models = Media::query();
        } else
        {
            $models = Media::filterByUser( is_user_can( 'media_other_index' ) ? 'all' : auth()->user() );
        }
        //todo: remove this in production
        $models = Media::query();

        /**
         * get all crop size urls
         */
        $url    = Media::getFakeUrl();
        $select = "id, title, alt, size, created_at";
        foreach ( Media::IMAGE_CROP_SIZES as $name => $sizes )
        {
            $_name = '-' . $name;
            if ( $name == 'full' )
            {
                $_name = '';
            }
            $select .= ", CONCAT( \"$url\", path, name, \"$_name\", \".\", ext ) as image_$name";
            $select .= ", CONCAT( name, \".\", ext ) as file_name";
        }
        $models->orderBy( 'created_at', 'desc' )
            ->select( DB::raw( $select ) );
        $rows_per_page = 25;

        /**
         * filters provided by client
         */
        if ( $request->get( 'search' ) )
        {
            $models->where( function ( $query ) use ( $request ) {
                $query->where( 'title', 'LIKE', "%$request->search%" )
                    ->orWhere( 'full_path', 'LIKE', "%$request->search%" );
            } );
        }

        if ( $folder )
        {
            $models->where( 'folder_id', $folder );
        } else if ( ! $folder && $show_public_folder )
        {
            $models->where( 'folder_id', $folder );
        }

        $result = $models->paginate( $rows_per_page )->items();

        $result = Arr::where( $result, function( Media $media ){
            $media->date = JalaliHelper::jdate( 'H:i Y/n/d', strtotime( $media->created_at ), 'en' );
            $media->size = media_get_file_size( $media->size );
            unset( $media->created_at );
            return $media;
        });

        return $result;
    }
}
