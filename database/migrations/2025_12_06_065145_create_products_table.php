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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auto_id');   // ক্যাটেগরি (foreign key)
            $table->string('product_name_en');           // ইংরেজি নাম
            $table->string('product_name_bn');           // বাংলা নাম
            $table->unsignedBigInteger('category_id');   // ক্যাটেগরি (foreign key)
            $table->decimal('price', 10, 2);             // দাম
            $table->decimal('discount_price', 10, 2)->nullable(); // ডিসকাউন্ট দাম
            $table->string('image')->nullable();         // single image path
            $table->json('images')->nullable();          // multiple images as JSON
            $table->string('video')->nullable();         // video path
            $table->text('description_en')->nullable();     
            $table->text('description_bn')->nullable();     
            $table->text('short_description_en')->nullable();     
            $table->text('short_description_bn')->nullable();     
            $table->text('calculation')->nullable();     
            $table->boolean('is_in_stock')->default(true); 
            $table->text('seller_details')->nullable(); 
            $table->string('mobile_no')->nullable();     
            $table->integer('quantity')->default(0);     
            $table->tinyInteger('status')->default(1);   // 1=active, 0=inactive
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
        Schema::dropIfExists('products');
    }
};
