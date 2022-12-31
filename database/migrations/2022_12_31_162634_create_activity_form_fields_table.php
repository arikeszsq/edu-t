<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_form_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->default('NULL')->nullable();
            $table->string('field_name')->default('NULL')->nullable();
            $table->string('field_en_name')->default('NULL')->nullable();
            $table->integer('type')->default('NULL')->nullable()->comment('1 文本框 2单选框 3多选框');
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
        Schema::dropIfExists('activity_form_fields');
    }
}
