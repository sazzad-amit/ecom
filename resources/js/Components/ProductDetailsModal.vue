<template>
  <div
    v-if="product"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white w-full max-w-4xl rounded-xl relative max-h-[90vh] overflow-auto">
      <button
        @click="$emit('close')"
        class="absolute top-3 right-3 text-xl z-10 w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-full"
      >
        ✖
      </button>

      <div class="grid md:grid-cols-2 gap-6 p-6">
        <!-- Images -->
        <div>
          <img
            :src="getImageUrl(product.image_url)"
            :alt="product.product_name_en"
            class="w-full h-72 object-cover rounded-lg"
            @error="handleImageError"
          />

          <div class="flex gap-2 mt-3 overflow-x-auto">
            <img
              v-for="(img, i) in product.images_urls || []"
              :key="i"
              :src="getImageUrl(img)"
              class="w-20 h-20 object-cover rounded border hover:border-blue-500 cursor-pointer"
              @click="product.image_url = img"
            />
          </div>
        </div>

        <!-- Details -->
        <div class="space-y-4">
          <h2 class="text-2xl font-bold">
            {{ product.product_name_en }}
          </h2>

          <p class="text-gray-500">
            Category:
            <span class="font-medium text-gray-800">
              {{ product.category?.category_name_en || 'Uncategorized' }}
            </span>
          </p>

          <p class="text-gray-600 leading-relaxed">
            {{ product.description_en }}
          </p>

          <div class="flex items-center gap-3">
            <span class="text-2xl font-bold text-blue-600">
              ৳ {{ formatPrice(product.discount_price) }}
            </span>
            <span
              v-if="product.discount_price < product.price"
              class="line-through text-gray-400 text-lg"
            >
              ৳ {{ formatPrice(product.price) }}
            </span>
          </div>

          <p
            class="text-sm font-medium py-1 px-3 rounded-full inline-block"
            :class="product.is_in_stock ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100'"
          >
            {{ product.is_in_stock ? 'In Stock' : 'Out of Stock' }}
          </p>

          <button
            @click="$emit('add-to-cart', product)"
            :disabled="!product.is_in_stock"
            class="w-full py-3 rounded-lg text-white font-medium transition-colors"
            :class="product.is_in_stock
              ? 'bg-blue-600 hover:bg-blue-700'
              : 'bg-gray-400 cursor-not-allowed'"
          >
            Add to Cart
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:3000';

defineProps({
  product: {
    type: Object,
    required: true
  }
});

defineEmits(['close', 'add-to-cart']);

const getImageUrl = (imagePath) => {
  if (!imagePath) return '/placeholder-image.jpg';
  if (imagePath.startsWith('http')) return imagePath;
  return `${API_BASE_URL}${imagePath.startsWith('/') ? '' : '/'}${imagePath}`;
};

const handleImageError = (event) => {
  event.target.src = '/placeholder-image.jpg';
};

const formatPrice = (price) => {
  return Number(price || 0).toLocaleString('en-BD');
};
</script>