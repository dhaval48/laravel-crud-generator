<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileUploadDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_upload_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('file_upload_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('path_name')->nullable();
            $table->timestamps();

            $table->foreign('file_upload_id')->references('id')->on('file_uploads')
                            ->onUpdate("cascade")
                            ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_upload_details');
    }
}
