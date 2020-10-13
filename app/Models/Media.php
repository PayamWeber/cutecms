<?php

namespace App\Models;

use App\User;
use Composer\Downloader\FileDownloader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use JsonSchema\Uri\Retrievers\FileGetContents;

class Media extends BaseModel
{
	protected $table    = 'media';
	protected $fillable = [
		'name',
		'path',
		'type',
		'created_at',
		'updated_at',
	];

	/**
	 * this is the all file mime types
	 */
	const ALLOWED_FILE_MIME_TYPES = [
		'application/zip',
		'application/octet-stream',
		'application/x-zip-compressed',
		'application/x-rar-compressed',
		'application/pdf',
		'text/plain',
		'video/mp4',
	];

	/**
	 * this is the allowed image mime types only
	 */
	const ALLOWED_IMAGE_MIME_TYPES = [
		'image/jpeg',
		'image/gif',
		'image/png',
		'image/svg+xml',
		'image/svg',
		'image/x-icon',
	];

	/**
	 * this is the mime types that can be cropped
	 */
	const ALLOWED_CROPABLE_IMAGE_TYPES = [
		'image/jpeg',
		'image/png',
	];

	/**
	 * this is the mime types that can be cropped
	 */
	const ALLOWED_CROPABLE_IMAGE_EXT = [
		'jpeg',
		'jpg',
		'png',
	];

	/**
	 * this is the uploads path
	 */
	const UPLOAD_PATH = '/public/uploads/';
    const FAKE_PATH   = '/public/uploads/';

	/**
	 *
	 */
	const MAX_FILE_SIZE    = 10485760;
	const IMAGE_CROP_SIZES = [
		'thumbnail' => [
			'w' => 150, // don't remove
			'h' => 150, // don't remove
		],
		'medium' => [
			'w' => 600,
			'h' => null,
		],
		'full' => [
			'w' => null, // don't remove
			'h' => null, // don't remove
		],
	];
	public static $image_size_names = [
		'thumbnail' => 'بند انگشتی', // don't remove
		'medium' => 'متوسط',
		'full' => 'واقعی', // don't remove
	];

	public function __construct()
	{
		self::$image_size_names[ 'thumbnail' ] = lang( 'Thumbnail' );
		self::$image_size_names[ 'medium' ]    = lang( 'Medium' );
		self::$image_size_names[ 'full' ]      = lang( 'Full' );
	}

	/**
	 * this function return latest files have been uploaded
	 *
	 * @param bool $return_html
	 *
	 * @return array|null|object|string
	 */
	public static function get_latest_files( $return_html = TRUE, $media_type = NULL, $number = 100 )
	{
		$media_type = $media_type ?? 'file';

		if ( $media_type == 'image' || $media_type == 'tinymce' )
			$files = self::whereIn( 'type', self::ALLOWED_IMAGE_MIME_TYPES )->orderByDesc( 'created_at' )->take( $number )->get();
		else
			$files = self::all()->sortByDesc( 'created_at' )->take( $number );

		if ( $return_html == FALSE )
		{
			return $files;
		}

		$html = '';
		$html .= "
        <div class=\"ajax-file-upload-container latest-files\">
        ";
		if ( $files )
		{
			foreach ( $files as $file )
			{
				$file_path = public_path( self::UPLOAD_PATH . $file->full_path );
				if ( ! file_exists( $file_path ) )
				{
					continue;
				}
				$file_name       = mb_substr( $file->name, 0, 37 ) . '...';
				$file_full_name  = basename( $file_path );
				$file_size       = media_get_file_size( filesize( $file_path ) );
				$image_url       = [];
				$data_image_size = '';
				$thumbnail       = '';
				if ( self::IMAGE_CROP_SIZES )
				{
					foreach ( self::IMAGE_CROP_SIZES as $CROP_NAME => $CROP_SIZE )
					{
						if ( in_array( $file->type, self::ALLOWED_CROPABLE_IMAGE_TYPES ) )
						{
							$image_url[ $CROP_NAME ] = $file->url( $CROP_NAME );
							$data_image_size         .= "data-$CROP_NAME='$image_url[$CROP_NAME]' ";
						} else if ( in_array( $file->type, self::ALLOWED_IMAGE_MIME_TYPES ) )
						{
							$image_url[ $CROP_NAME ] = $file->url();
							$data_image_size         .= "data-$CROP_NAME='$image_url[$CROP_NAME]' ";
						}
					}
				}
				if ( in_array( $file->type, self::ALLOWED_IMAGE_MIME_TYPES ) )
				{
					$thumbnail = "background-image: url(\"$image_url[thumbnail]\")";
				}
				$html .= "
                <div class=\"ajax-file-upload-statusbar past-file\" data-caption='$file->title' data-file-name='$file_full_name' data-file-id='$file->id' $data_image_size title='$file_full_name'>
                    <div class=\"ajax-file-upload-preview\" style='$thumbnail'></div>
                    <div class=\"ajax-file-upload-filename\">{$file_name}
                        <span class=\"ajax-file-upload-filename-size\">( $file_size )</span>
                    </div>
                    <div style=\"\" class=\"ajax-file-upload-red ajax-file-upload-delete\"><i class='la la-close'></i></div>
                    <div style=\"\" class=\"ajax-file-upload-blue ajax-file-upload-select\">انتخاب</div>
                </div>
                ";
			}
		}
		$html .= "</div>";

		return $html;
	}

    /**
     * @param array $args
     *
     * @return string
     */
	public static function make_media_uploader( array $args = [] )
	{
		$defaults = [
			'type' => 'image',
			'id' => uniqid( rand( 1, 999 ) ),
			'name' => '',
			'value' => '',
		];
		$args     = (object) array_merge( $defaults, $args );

		$_value                      = htmlspecialchars( $args->value );
		$_remove_button_hidden_class = $_value ? '' : 'hidden';
		$file_single_name            = lang( 'Upload File' );
		$file                        = $_value ? self::find( $_value ) : '';
		$file_name                   = is_object( $file ) ? basename( $file->path ) : '';
		$file_alt                    = is_object( $file ) ? $file->title : '';
		$file_url                    = is_object( $file ) ? $file->url( 'thumbnail' ) : '';

		if ( $args->type == 'image' )
			$file_single_name = lang( 'Upload Image' );
		if ( $args->type == 'tinymce' )
			$file_single_name = lang( 'Put Image' );

		return "
        <div id='$args->id' class='media-upload-buttons' data-upload-type='$args->type'>
            " . ( ( $args->type == 'file' ) ? "<p class='file-name'>$file_name</p>" : '' ) . "
            " . ( ( $args->type == 'image' ) ? "<img src='$file_url' alt='$file_alt'>" : '' ) . "
            <input type='hidden' name='{$args->name}' value='$_value' >
            <div class='this-buttons'>
                <button class='button delete-current-media $_remove_button_hidden_class'>حذف</button>
                <button class='button open-media-modal-button'>$file_single_name</button>
            </div>
        </div>
        ";
	}

    /**
     * @return array
     */
    public static function getAllowedMimeTypes()
    {
        return array_merge( self::ALLOWED_FILE_MIME_TYPES, self::ALLOWED_IMAGE_MIME_TYPES, self::ALLOWED_CROPABLE_IMAGE_TYPES );
    }

    /**
     * @return mixed
     */
    public static function getMaxFileSize()
    {
        $default_max_size = ini_get( 'upload_max_filesize' );

        //		return env( 'MAX_UPLOAD_SIZE', self::convertFileSizeToByte( $default_max_size ) );
        return config( 'base.max_upload_size' ) ? : self::convertFileSizeToByte( $default_max_size );
    }

    /**
     * @param $val
     *
     * @return false|int|string
     */
    public static function convertFileSizeToByte( $val )
    {
        $val = trim( $val );

        if ( is_numeric( $val ) )
            return $val;

        $last = strtolower( $val[ strlen( $val ) - 1 ] );
        $val  = substr( $val, 0, -1 ); // necessary since PHP 7.1; otherwise optional

        switch ( $last )
        {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * @param $name
     *
     * @return string
     */
    public static function cleanFileName( $name )
    {
        if ( is_string( $name ) && $name )
        {
            $name = str_replace( ' ', '-', $name );
            preg_match_all( '/([A-Za-z0-9\-\_]*[^\x00-\xFF]*\s*)/u', $name, $output_array );
            $safe = implode( '', $output_array[ 0 ] );

            if ( mb_strlen( implode( '', $output_array[ 0 ] ) ) > 100 )
                $safe = mb_substr( $safe, 0, 90 );

            return $safe;
        } else
        {
            return '';
        }
    }

    /**
     * this function returns file name without extension
     *
     * @param $file_name
     *
     * @return string
     */
    public static function getOnlyName( $file_name )
    {
        $file_name_exploded = explode( '.', $file_name );

        // file name without file format
        array_pop( $file_name_exploded );

        return implode( '.', $file_name_exploded );
    }

    /**
     * this function returns file extension
     *
     * @param $file_name
     *
     * @return mixed
     */
    public static function getExt( $file_name )
    {
        $file_name_exploded = explode( '.', $file_name );

        return end( $file_name_exploded );
    }

    /**
     * @param Integer|Media $media
     * @param string        $size
     *
     * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public static function getMediaUrl( $media, $size = 'full' )
    {
        $file = $media instanceof Media ? $media : self::find( $media );
        if ( ! $file )
            return false;

        $is_cropable = in_array( $file->type, self::ALLOWED_CROPABLE_IMAGE_TYPES );

        if ( $size == 'full' )
        {
            $size = '';
        } else if ( isset( self::IMAGE_CROP_SIZES[ $size ] ) && $is_cropable )
        {
            $size = '-' . $size;
        } else
        {
            $size = '';
        }

        $final_file_name = $file->title . $size . '.' . $file->ext;

        return self::getFakeUrl( $file->path . $final_file_name );
    }

    /**
     * @return string
     */
    public static function getFakeUrl( $path = '' )
    {
        $fake_url = config( 'base.media_fake_url' );

        if ( $path )
        {
            return $fake_url ? esc_url( $fake_url . $path ) : esc_url( url( self::FAKE_PATH . $path ) );
        } else
        {
            return $fake_url ? esc_url( $fake_url ) : esc_url( url( self::FAKE_PATH ) . '/' );
        }
    }

    /**
     * @return mixed
     */
    public function caption()
    {
        return $this->title;
    }

    /**
     * @param string $size
     *
     * @return string
     */
    public function url( $size = 'full' )
    {
        $is_cropable = in_array( $this->type, self::ALLOWED_CROPABLE_IMAGE_TYPES );

        if ( $size == 'full' )
        {
            $size = '';
        } else if ( isset( self::IMAGE_CROP_SIZES[ $size ] ) && $is_cropable )
        {
            $size = '-' . $size;
        } else
        {
            $size = '';
        }

        $final_file_name = $this->name . $size . '.' . $this->ext;

        return self::getFakeUrl( $this->path . $final_file_name );
    }

    /**
     * @param array  $files
     * @param string $type
     * @param bool   $is_admin
     *
     * @return array|mixed|string
     */
    public static function insertAndUploadFiles( array $files, $type = 'image', bool $is_admin = true )
    {
        $medias = [];
        if ( $files )
        {
            $user = \Auth::user();
            foreach ( $files as $file )
            {
                if ( $file instanceof UploadedFile )
                {
                    $errors    = [];
                    $path      = self::getAppendUploadPath( false, true ) . date( 'Y/m' ) . '/';
                    $file_name = $file->getClientOriginalName();
                    // file name without file format
                    $clean_name = $file_title = Media::cleanFileName( Media::getOnlyName( $file_name ) );
                    $just_name  = date( 'mdHis' ) . '-' . $clean_name;

                    $file_ext  = Media::getExt( $file_name );
                    $file_mime = $file->getClientMimeType();
                    $file_size = $file->getSize();
                    $file_name = $just_name . '.' . $file_ext;

                    self::findAndChangeFileName( $file_name, $just_name, $file_ext );

                    // check file size
                    if ( $file_size > Media::getMaxFileSize() )
                    {
                        $errors[] = 'media.max_size';
                    } // check file mime type
                    else if ( $type == 'image' && ( $user->can( 'media_store_file' ) || $user->can('media_store_image' ) ) )
                    {
                        if ( ! in_array( $file_mime, Media::ALLOWED_IMAGE_MIME_TYPES ) )
                        {
                            $errors[] = 'insert_only_image';
                        }
                    } else if ( $type == 'file' && $user->can( 'media_store_file' ) )
                    {
                        if ( ! in_array( $file_mime, Media::getAllowedMimeTypes() ) )
                        {
                            $errors[] = 'file_format_fail';
                        }
                    }

                    if ( $errors )
                    {
                        return reset( $errors );
                    } else
                    {
                        $media            = new Media();
                        $media->user_id   = \Auth::id();
                        $media->name     = $file_title;
                        $media->path = $path . $file_name;
                        $media->type      = $file_mime;
                        $media->size      = $file_size;
                        $media->is_admin  = intval( $is_admin );

                        if ( $media->save() )
                        {
                            $medias[] = $media;
                            $upload_path = public_path( self::getUploadPath() . self::getAppendUploadPath( true ) . date( '/Y/m' ) . '/' );

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

                                        $img = Image::make( public_path( self::getUploadPath() ) . '/' . $media->path );

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

                            if ( Media::IMAGE_CROP_SIZES && in_array( $file_mime, Media::ALLOWED_CROPABLE_IMAGE_TYPES ) )
                            {
                                foreach ( Media::IMAGE_CROP_SIZES as $CROP_NAME => $CROP_SIZE )
                                {
                                    $image_url[ $CROP_NAME ] = get_media_url( $media, $CROP_NAME );
                                }
                            } else
                            {
                                $image_url[ 'full' ] = get_media_url( $media );
                            }
                        } else
                        {
                            return 'media.database_fail';
                        }
                    }
                }
            }
        }

        return $medias;
    }

    /**
     * @param        $url
     * @param string $filename
     *
     * @return Media|bool
     */
    public static function uploadFileFromUrl( $url, $filename = '' )
    {
        $file_content = file_get_contents( $url );
        $filename = ( $filename ? : basename( $url ) );
        $only_name = Media::getOnlyName( $filename );
        $file_ext  = Media::getExt( $filename );

        $upload_path = public_path( self::UPLOAD_PATH . date( 'Y/m' ) . '/' );

        if ( ! is_dir( $upload_path ) ) {
            mkdir( $upload_path, 0775, true );
        }
        $file_path = $upload_path . '/' . $filename;

        file_put_contents( $file_path, $file_content );

        if ( in_array( $file_ext, Media::ALLOWED_CROPABLE_IMAGE_EXT ) ) {
            if ( Media::IMAGE_CROP_SIZES ) {
                foreach ( Media::IMAGE_CROP_SIZES as $_crop_name => $_crop_size ) {
                    if ( $_crop_name == 'full' )
                        continue;

                    $img = Image::make( $file_path );

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
                    $img->save( $upload_path . $only_name . '-' . $_crop_name . '.' . $file_ext );
                }
            }
        }
        $image_url = [];

        $media            = new Media();
        $media->user_id   = Auth::id() ? : User::first()->id;
        $media->title     = $only_name;
        $media->alt       = '';
        $media->path      = $path = date( 'Y/m' ) . '/';
        $media->name      = $only_name;
        $media->ext       = $file_ext;
        $media->full_path = $path . $filename;
        $media->type      = 'image/jpeg';
        $media->size      = 10;

        if ( $media->save() ) {

            return $media;
        }else{
            return false;
        }
    }
}
