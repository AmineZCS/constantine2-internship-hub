<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('supervisor_id');
            $table->string('position');
            $table->text('description')->nullable();
            $table->string('location');
            $table->enum('status', ['open', 'closed', 'filled'])->default('open');
            $table->text('requirements')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('supervisor_id')->references('id')->on('supervisors')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
