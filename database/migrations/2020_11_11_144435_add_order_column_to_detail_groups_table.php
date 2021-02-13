<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zareismail\Details\Models\Detail;

class AddOrderColumnToDetailGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_groups', function (Blueprint $table) {
            $table->integer('order')->default(0); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_groups', function (Blueprint $table) {
            $table->dropColumn('order'); 
        }); 
    } 
}
