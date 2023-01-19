<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserViewCountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_view_count', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->default('NULL')->nullable();
            $table->integer('user_id')->default('NULL')->nullable();
            $table->string('has_info')->default('NULL')->nullable();
            $table->string('has_sign')->default('NULL')->nullable();
            $table->integer('view_num')->default('NULL')->nullable();
            $table->dateTime('view_at')->default('NULL')->nullable();
            $table->integer('share_user_id')->default('NULL')->nullable();
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
        Schema::dropIfExists('user_view_count');
    }
}
