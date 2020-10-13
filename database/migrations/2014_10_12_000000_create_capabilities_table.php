<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'capabilities', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->unsignedTinyInteger( 'parent' );
            $table->string( 'name' );
            $table->string( 'title' );
            $table->text( 'route' );

            $table->foreign('parent')->references('id')->on('capability_cats')->onDelete('cascade');
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'capabilities' );
    }
}
