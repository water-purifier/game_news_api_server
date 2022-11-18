<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('tag_name_en')->nullable();
            $table->string('tag_name_ko')->nullable();
            $table->string('tag_name_cn')->nullable();
            $table->tinyInteger('is_view')->default(1);
            $table->timestamps();
        });

        Schema::create('post_tag',function (Blueprint $table){
           $table->id();
           $table->foreignId('post_id');
           $table->foreignId('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('post_tag');
    }
};
