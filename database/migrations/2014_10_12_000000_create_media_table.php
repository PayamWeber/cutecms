<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'media', function ( Blueprint $table ) {
            $table->bigIncrements( 'id' );
            $table->unsignedInteger( 'user_id' )->nullable( true );
            $table->unsignedInteger( 'folder_id' )->nullable(true)->default(null);
            $table->string( 'title' )->nullable(true)->default(null);
            $table->string( 'alt' )->nullable(true)->default(null);
            $table->string( 'path' )->nullable(true)->default(null);
            $table->string( 'name' )->nullable(true)->default(null);
            $table->string( 'ext' )->nullable(true)->default(null);
            $table->string( 'full_path' )->nullable(true)->default(null);
            $table->string( 'type' )->nullable(true)->default(null);
            $table->unsignedBigInteger( 'size' )->nullable(true)->default(0);
            $table->timestamp();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set_null');
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'media' );
    }
}
