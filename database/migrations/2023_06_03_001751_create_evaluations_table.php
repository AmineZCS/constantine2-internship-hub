<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('supervisor_id');
            $table->foreign('supervisor_id')->references('id')->on('supervisors')->onDelete('cascade');
            $table->unsignedBigInteger('general')->nullable();
            $table->unsignedBigInteger('skills')->nullable();
            $table->unsignedBigInteger('initiative')->nullable();
            $table->unsignedBigInteger('imagination')->nullable();
            $table->unsignedBigInteger('knowledge')->nullable();
            $table->unsignedTinyInteger('total_mark')->nullable();
            $table->string('global_appreciation')->nullable();
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
