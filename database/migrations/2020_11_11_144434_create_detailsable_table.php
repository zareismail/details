<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zareismail\Details\Models\Detail;

class CreateDetailsableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detailsable', function (Blueprint $table) {
            $table->id();
            $table->text('value');    
            $table->foreignId('detail_id')->constrained(); 
            $table->morphs('detailsable'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detailsable');
    } 
}
