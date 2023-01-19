<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->default('NULL')->nullable();
            $table->integer('share_user_id')->default('NULL')->nullable();
            $table->integer('share_num')->default('NULL')->nullable();
            $table->integer('sign_num')->default('NULL')->nullable();
            $table->decimal('pay_total_num')->default('NULL')->nullable();
            $table->integer('red_bag_num')->default('NULL')->nullable();
            $table->decimal('red_bag_total_num')->default('NULL')->nullable();
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
        Schema::dropIfExists('share');
    }
}
