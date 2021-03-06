<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'pages', function ( Blueprint $table ) {
            $table->bigIncrements( 'id' );
            $table->unsignedInteger( 'user_id' )->nullable( true )->default(null);
            $table->unsignedTinyInteger( 'status' )->default(0);
            $table->string( 'slug' )->nullable(true)->default(null);
            $table->string( 'title' )->nullable(true)->default(null);
            $table->mediumText( 'content' );
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'pages' );
    }
}
