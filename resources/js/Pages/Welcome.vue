<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- ================= Header ================= -->
    <header class="bg-white shadow sticky top-0 z-40">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-600">ShopEasy</h1>

        <button
          @click="openCart"
          class="relative bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
        >
          🛒 Cart
          <span
            v-if="cartStore.cart.length"
            class="absolute -top-2 -right-2 bg-red-600 text-xs w-5 h-5 flex items-center justify-center rounded-full"
          >
            {{ cartStore.cart.length }}
          </span>
        </button>
      </div>
    </header>

    <!-- ================= Body ================= -->
    <div class="container mx-auto px-4 py-8 flex gap-6">
      <!-- Sidebar -->
      <aside class="w-64 hidden lg:block">
        <div class="bg-white rounded-xl shadow p-4 space-y-4 sticky top-24">
          <input
            v-model="searchQuery"
            placeholder="Search product..."
            class="w-full px-4 py-2 rounded-lg bg-gray-100 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />

          <div class="space-y-2">
            <button
              v-for="cat in categories"
              :key="cat"
              @click="selectedCategory = cat"
              class="w-full px-3 py-2 rounded-lg text-left transition-colors"
              :class="selectedCategory === cat
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 hover:bg-gray-200'"
            >
              {{ cat }}
            </button>
          </div>
        </div>
      </aside>

      <!-- ================= Products ================= -->
      <main class="flex-1">
        <h2 class="text-2xl font-bold mb-2">{{ selectedCategory }}</h2>
        <p class="text-gray-500 mb-6">{{ filteredProducts.length }} products found</p>

        <div
          v-if="filteredProducts.length"
          class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
        >
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="bg-white rounded-xl shadow hover:shadow-xl transition-all duration-300 overflow-hidden"
          >
            <!-- Image -->
            <div class="relative h-48 bg-gray-100">
              <img
                :src="getImageUrl(product.image_url)"
                :alt="product.product_name_en"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
              <span
                class="absolute top-2 left-2 px-2 py-1 text-xs rounded text-white font-medium"
                :class="product.is_in_stock ? 'bg-green-600' : 'bg-red-600'"
              >
                {{ product.is_in_stock ? 'In Stock' : 'Out of Stock' }}
              </span>
            </div>

            <!-- Content -->
            <div class="p-4 space-y-3">
              <h3 class="font-semibold text-lg">{{ product.product_name_en }}</h3>

              <p class="text-sm text-gray-600 line-clamp-2">
                {{ product.short_description_en }}
              </p>

              <div class="flex items-center gap-2">
                <span class="text-lg font-bold text-blue-600">
                  ৳ {{ formatPrice(product.discount_price) }}
                </span>
                <span
                  v-if="product.discount_price < product.price"
                  class="text-sm line-through text-gray-400"
                >
                  ৳ {{ formatPrice(product.price) }}
                </span>
              </div>

              <!-- Buttons -->
              <div class="flex gap-2 pt-2">
                <button
                  @click="addToCart(product)"
                  :disabled="!product.is_in_stock"
                  class="flex-1 py-2 rounded-lg text-sm transition-colors"
                  :class="product.is_in_stock
                    ? 'bg-blue-600 hover:bg-blue-700 text-white'
                    : 'bg-gray-300 cursor-not-allowed text-gray-500'"
                >
                  Add to Cart
                </button>

                <button
                  @click="openDetails(product)"
                  class="flex-1 py-2 rounded-lg text-sm border border-blue-600 text-blue-600 hover:bg-blue-50 transition-colors"
                >
                  View Details
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-20 text-gray-500">
          <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p>No products found</p>
          <p class="text-sm mt-2">Try changing your search or category</p>
        </div>
      </main>
    </div>

    <!-- ================= Product Details Modal ================= -->
    <ProductDetailsModal
      v-if="detailsOpen"
      :product="selectedProduct"
      @close="detailsOpen = false"
      @add-to-cart="addToCart"
    />

    <!-- ================= Cart Modal ================= -->
    <CartModal
      v-if="cartOpen"
      @close="cartOpen = false"
      @proceed-to-checkout="handleCheckout"
    />
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from "vue";
import axios from "axios";
import { useCartStore } from "@/stores/cart";
import CartModal from "@/components/CartModal.vue";
import ProductDetailsModal from "@/components/ProductDetailsModal.vue";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:3000';

/* ================= State ================= */
const products = ref([]);
const searchQuery = ref("");
const selectedCategory = ref("All Products");
const detailsOpen = ref(false);
const selectedProduct = ref({});
const cartOpen = ref(false);

const categories = [
  "All Products",
  "Electronics",
  "Fashion",
  "Home",
  "Sports",
];

/* ================= Stores ================= */
const cartStore = useCartStore();

/* ================= Computed Properties ================= */
const filteredProducts = computed(() => {
  let filtered = products.value;
  
  // Filter by category
  if (selectedCategory.value !== "All Products") {
    filtered = filtered.filter(product => 
      product.category?.category_name_en === selectedCategory.value
    );
  }
  
  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(product =>
      product.product_name_en?.toLowerCase().includes(query) ||
      product.short_description_en?.toLowerCase().includes(query)
    );
  }
  
  return filtered;
});

/* ================= Methods ================= */
const fetchProducts = async () => {
  try {
    const res = await axios.get(`${API_BASE_URL}/api/products-landing-search`, {
      params: {
        q: searchQuery.value || undefined,
        category: selectedCategory.value === "All Products"
          ? undefined
          : selectedCategory.value,
      },
    });
    products.value = res.data.data?.data || [];
  } catch (error) {
    console.error('Error fetching products:', error);
    products.value = [];
  }
};

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

const openDetails = (product) => {
  selectedProduct.value = { ...product };
  detailsOpen.value = true;
};

const addToCart = (product) => {
  if (!product.is_in_stock) return;
  cartStore.addToCart(product);
};

const openCart = () => {
  cartOpen.value = true;
};

const handleCheckout = () => {
  cartOpen.value = false;
  // You can add any post-checkout logic here
  console.log('Checkout completed');
};

/* ================= Watch ================= */
watch([searchQuery, selectedCategory], fetchProducts);

/* ================= Lifecycle ================= */
onMounted(() => {
  fetchProducts();
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>