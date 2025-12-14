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
        Schema::create('sources', function (Blueprint $table) {
            $table->id(); // primary key
            $table->string('source_name_en');
            $table->string('source_name_bn')->nullable();
            $table->string('source_auto_id')->unique();
            $table->string('mobile_no')->nullable();
            $table->text('details_en')->nullable();
            $table->text('details_bn')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('created_by');    
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
