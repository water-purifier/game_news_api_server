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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title_en')->nullable();
            $table->string('title_ko')->nullable();
            $table->string('title_cn')->nullable();
            $table->string('description_en')->nullable();
            $table->string('description_ko')->nullable();
            $table->string('description_cn')->nullable();
            $table->longText('text_html')->nullable();
            $table->longText('text_en')->nullable();
            $table->longText('text_ko')->nullable();
            $table->longText('text_cn')->nullable();
            $table->string('author_ori')->nullable();
            $table->string('date_ori')->nullable();
            $table->string('url_ori')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->integer('thumbs_up')->default(0);
            $table->integer('thumbs_down')->default(0);
            $table->bigInteger('view_count')->default(0);
            $table->tinyInteger('is_view')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
