<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zareismail\Details\Models\Detail;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->id();
            $table->json('name'); 
            $table->json('help')->nullable(); 
            $table->json('config')->nullable(); 
            $table->json('options')->nullable(); 
            $table->enum('field', Detail::fields())->default(head(Detail::fields())); 
            $table->foreignId('group_id')->constrained('detail_groups');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('details');
    } 
}
