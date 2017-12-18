<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            //
            //$table->integer('listorder')->default(0);
            //$table->timestamps();
            //$table->softDeletes();
            $table->string('jumpurl');
        });
        //update articles set listorder = (select listorder from categories_relationships where categories_relationships.object_id = articles.id);
        //update articles set post_type = (select term_id from categories_relationships where categories_relationships.object_id = articles.id);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            //
        });
    }
}
