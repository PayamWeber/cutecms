<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'roles', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->string( 'name' );
            $table->string( 'title' );
            $table->json( 'capabilities' )->nullable( true )->default( null );
            $table->unsignedTinyInteger( 'is_admin' )->default( 0 );
            $table->unsignedTinyInteger( 'is_default' )->default( 0 );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'roles' );
    }
}
