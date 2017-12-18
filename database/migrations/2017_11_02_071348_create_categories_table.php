<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100)->index()->nullable();
            $table->string('description',250)->default(null);
            $table->unsignedInteger('parent')->index()->comment('父层id')->default(0);
            $table->string('seo_title',250)->default(null);
            $table->string('seo_keywords',250)->default(null);
            $table->string('seo_description',250)->default(null);
            $table->longText('content')->comment('内容');
            $table->tinyInteger('listorder')->comment('排序')->default(0);
            
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
        Schema::dropIfExists('categories');
    }
}
