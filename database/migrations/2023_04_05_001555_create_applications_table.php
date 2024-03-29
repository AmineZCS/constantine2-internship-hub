<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('internship_id')->constrained()->onDelete('cascade');
            $table->enum('admin_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('supervisor_status', ['pending', 'approved', 'rejected'])->default('pending');
            // num_attempts is used to keep track of how many times the student has applied for the same internship
            $table->integer('num_attempts')->default(1);
            $table->timestamps();
            $table->unique(['student_id', 'internship_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
