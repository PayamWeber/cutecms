<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'posts', function ( Blueprint $table ) {
            $table->bigIncrements( 'id' );
            $table->unsignedInteger( 'user_id' )->nullable( true )->default(null);
            $table->unsignedBigInteger( 'image_id' )->nullable( true )->default(null);
            $table->unsignedBigInteger( 'video_id' )->nullable( true )->default(null);
            $table->string( 'instagram_id' )->nullable( true )->default(null);
            $table->unsignedTinyInteger( 'status' )->default(0);
            $table->unsignedTinyInteger( 'type' )->default(0);
            $table->unsignedMediumInteger( 'views' )->default(0);
            $table->unsignedMediumInteger( 'likes' )->default(0);
            $table->string( 'slug' )->nullable(true)->default(null);
            $table->string( 'title' )->nullable(true)->default(null);
            $table->mediumText( 'content' );
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('image_id')->references('id')->on('media')->onDelete(null);
            $table->foreign('video_id')->references('id')->on('media')->onDelete(null);
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'posts' );
    }
}
