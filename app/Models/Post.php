<?php

namespace App\Models;

use App\Helpers\SafeMethods;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends BaseModel
{
    use SafeMethods, SoftDeletes;

    protected $table = 'posts';

    const STATUS_DRAFT   = 5;
    const STATUS_PUBLISH = 10;

    const TYPE_DEFAULT   = 5;
    const TYPE_VIDEO     = 10;
    const TYPE_GALLERY   = 15;
    const TYPE_QUOTE     = 20;
    const TYPE_INSTAGRAM = 25;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo( 'App\User', 'user_id' );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany( PostCategory::class, 'post_categories_relation', 'post_id', 'category_id' );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seo()
    {
        return $this->hasOne( PostSeo::class, 'post_id' );
    }

    /**
     * @param string $except
     *
     * @return array
     */
    public static function getStatuses( $except = '' )
    {
        $statuses = [
            self::STATUS_DRAFT => [
                'name' => lang( 'Draft' ),
                'color' => '#fff',
            ],
            self::STATUS_PUBLISH => [
                'name' => lang( 'Publish' ),
                'color' => '',
            ],
        ];

        if ( $except && is_array( $except ) ) {
            foreach ( $except as $name ) {
                if ( isset( $statuses[ $name ] ) ) {
                    unset( $statuses[ $name ] );
                }
            }
        } else if ( $except && isset( $statuses[ $except ] ) ) {
            unset( $statuses[ $except ] );
        }

        return $statuses;
    }

    /**
     * @param string $except
     *
     * @return array
     */
    public static function getTypes( $except = '' )
    {
        $statuses = [
            self::TYPE_DEFAULT => [
                'name' => lang( 'Default' ),
                'color' => '#fff',
            ],
            self::TYPE_VIDEO => [
                'name' => lang( 'Video' ),
                'color' => '#fff',
            ],
            self::TYPE_GALLERY => [
                'name' => lang( 'Gallery' ),
                'color' => '#fff',
            ],
            self::TYPE_QUOTE => [
                'name' => lang( 'Quote' ),
                'color' => '#fff',
            ],
            self::TYPE_INSTAGRAM => [
                'name' => lang( 'Instagram Post' ),
                'color' => '#fff',
            ],
        ];

        if ( $except && is_array( $except ) ) {
            foreach ( $except as $name ) {
                if ( isset( $statuses[ $name ] ) ) {
                    unset( $statuses[ $name ] );
                }
            }
        } else if ( $except && isset( $statuses[ $except ] ) ) {
            unset( $statuses[ $except ] );
        }

        return $statuses;
    }

    public function image()
    {
        return $this->belongsTo( Media::class, 'image_id' );
    }

    public function imageUrl( $size = 'thumbnail' )
    {
        return $this->image ? $this->image->url( $size ) : '';
    }
}
