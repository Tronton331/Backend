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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->string('name');
            $table->enum('choice_type', ['short answer', 'paragraph', 'date', 'time', 'multiple choice', 'dropdown', 'checkboxes']);
            $table->string('choices')->nullable();
            $table->boolean('is_required');

            $table->foreign('form_id')->references('id')->on('forms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
