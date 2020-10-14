<?php

namespace App\Models;

use App\Helpers\SafeMethods;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends BaseModel
{
    use SafeMethods, SoftDeletes;

    protected $table = 'tasks';

    const STATUS_TODO  = 5;
    const STATUS_DOING = 10;
    const STATUS_DONE  = 15;

    const PRIORITY_LOW    = 5;
    const PRIORITY_MEDIUM = 10;
    const PRIORITY_HIGH   = 15;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }

    /**
     * @param string $except
     *
     * @return array
     */
    public static function getStatuses( $except = '' )
    {
        $statuses = [
            self::STATUS_TODO => [
                'name' => lang( 'Todo' ),
            ],
            self::STATUS_DOING => [
                'name' => lang( 'Doing' ),
            ],
            self::STATUS_DONE => [
                'name' => lang( 'Done' ),
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
    public static function getPriorities( $except = '' )
    {
        $prios = [
            self::PRIORITY_LOW => [
                'name' => lang( 'Low' ),
                'color' => 'secondary',
            ],
            self::PRIORITY_MEDIUM => [
                'name' => lang( 'Medium' ),
                'color' => 'primary',
            ],
            self::PRIORITY_HIGH => [
                'name' => lang( 'High' ),
                'color' => 'danger',
            ],
        ];

        if ( $except && is_array( $except ) ) {
            foreach ( $except as $name ) {
                if ( isset( $prios[ $name ] ) ) {
                    unset( $prios[ $name ] );
                }
            }
        } else if ( $except && isset( $prios[ $except ] ) ) {
            unset( $prios[ $except ] );
        }

        return $prios;
    }
}
