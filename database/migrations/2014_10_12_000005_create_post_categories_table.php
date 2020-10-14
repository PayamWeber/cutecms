<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'post_categories', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->unsignedInteger( 'user_id' )->nullable( true )->default(null);
            $table->unsignedInteger( 'parent_id' )->default(0);
            $table->string( 'slug' )->nullable(true)->default(null);
            $table->string( 'title' )->nullable(true)->default(null);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete(null);
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'post_categories' );
    }
}
