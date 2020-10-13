<?php

namespace App\Http\Middleware;

use App\Models\Media;
use App\Models\Post;
use App\User;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use InstagramScraper\Instagram;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Helper\Psr16Adapter;

class FetchInsta
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle_asdfasdf( $request, Closure $next )
    {
        return $next($request);
        /** @var Carbon $last_load */
        $last_load = get_option( 'instagram_last_load', now()->subHours( 1 )->toDateTimeString() );
        $last_load = Carbon::parse( $last_load );

        if ( $last_load->copy()->addMinutes(30)->isPast() ) {
            //            $instagram = new \InstagramScraper\Instagram();
            $instagram = \InstagramScraper\Instagram::withCredentials(
                'pmweberz',
                '19734682gt',
                new Psr16Adapter('Files',
                    new ConfigurationOption(['defaultTtl' => 43200]) // Auth cache 1 day
                )
            );
            $instagram->login();
            $instagram->saveSession();
            $medias    = $instagram->getMedias( 'zerostrix', 5 );

            if ( $medias ) {
                foreach ( $medias as $post ) {
                    $find_same = Post::where( 'instagram_id', $post->getId() )->first();

                    if ( ! $find_same ) {
                        $image               = Media::uploadFileFromUrl( $post->getImageHighResolutionUrl(), $post->getId() . '.jpg' );
                        $model               = new Post;
                        $model->user_id      = User::first()->id;
                        $model->image_id     = $image ? $image->id : 0;
                        $model->instagram_id = $post->getId();
                        $model->title        = mb_substr( str_replace( "\n", '', Post::safeTitle( $post->getCaption() ) ), 0, 100 );
                        $model->slug         = mb_substr( str_replace( "\n", '', Post::safeName( $post->getCaption() ) ), 0, 100 );
                        $model->content      = $post->getCaption();
                        $model->status       = Post::STATUS_PUBLISH;
                        $model->type         = Post::TYPE_INSTAGRAM;
                        $model->views        = $post->getLikesCount() * 3;
                        $model->likes        = $post->getLikesCount();
                        $model->created_at   = Carbon::createFromTimestamp( $post->getCreatedTime() );
                        $model->updated_at   = Carbon::createFromTimestamp( $post->getCreatedTime() );

                        try {
                            $model->save();
                        } catch ( Exception $exception ) {
                        }
                    }
                }
            }
            update_option( 'instagram_last_load', now()->toDateTimeString() );
        }

        return $next( $request );
    }

    public function handle( $request, Closure $next )
    {
        if ( env('APP_ENV') == 'local' ){
            return $next($request);
        }

        /** @var Carbon $last_load */
        $last_load = get_option( 'instagram_last_load', now()->subHours( 3 )->toDateTimeString() );
        $last_load = Carbon::parse( $last_load );

        if ( $last_load->copy()->addMinutes(45)->isPast() ) {
            $curl = curl_init();

            curl_setopt_array( $curl, [
                CURLOPT_URL => "https://instagram9.p.rapidapi.com/api/instagram?kullaniciadi=zerostrix&lang=en",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 30,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "x-rapidapi-host: instagram9.p.rapidapi.com",
                    "x-rapidapi-key: 0bf87e47ccmsh85d5691a01d3b23p1d3d23jsn114fdf4614cf",
                ],
            ] );

            $response = curl_exec( $curl );
            $err      = curl_error( $curl );

            curl_close( $curl );

            if ( $err ) {
            } else {
                $response = json_decode( $response );
                $posts    = $response->posts ?? [];

                if ( $posts ) {
                    foreach ( $posts as $post ) {
                        $find_same = Post::where( 'instagram_id', $post->id )->first();

                        if ( ! $find_same ) {
                            $image               = Media::uploadFileFromUrl( $post->attachments->link, $post->id . '.jpg' );
                            $model               = new Post;
                            $model->user_id      = User::first()->id;
                            $model->image_id     = $image ? $image->id : 0;
                            $model->instagram_id = $post->id;
                            $model->title        = mb_substr( str_replace( "\n", ' ', Post::safeTitle( $post->text ) ), 0, 90 );
                            $model->slug         = mb_substr( str_replace( "\n", ' ', Post::safeName( $post->text ) ), 0, 90 );
                            $model->content      = $post->text;
                            $model->status       = Post::STATUS_PUBLISH;
                            $model->type         = Post::TYPE_INSTAGRAM;
                            $model->views        = $post->likeCount * 3;
                            $model->likes        = $post->likeCount;
                            $model->created_at   = Carbon::createFromTimestamp( $post->timestamp );
                            $model->updated_at   = Carbon::createFromTimestamp( $post->timestamp );

                            try{
                                $model->save();
                            }catch ( \Exception $exception ){

                            }
                        }
                    }
                }
            }
            update_option( 'instagram_last_load', now()->toDateTimeString() );
        }

        return $next( $request );
    }
}
