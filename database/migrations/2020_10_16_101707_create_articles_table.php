<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('article_id');   // auto increment の unsigned INTカラム
            $table->string('title');            // VARCHARカラム
            $table->text('body');               // TEXTカラム
            $table->integer('category_id');
            $table->softDeletes();              // ソフトデリート（論理削除）用の deleted_at カラム（TIMESTAMP型）
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
        Schema::dropIfExists('articles');
    }
}
