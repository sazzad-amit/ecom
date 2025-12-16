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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name_en');
            $table->string('category_name_bn');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('created_by');    
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Optional: parent_id কে foreign key বানাতে চাইলে
            // $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
