<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">

    <!-- ================= Header ================= -->
    <header class="bg-white shadow sticky top-0 z-40">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-600">ShopEasy</h1>

        <button
          @click="cartOpen = true"
          class="relative bg-blue-600 text-white px-4 py-2 rounded-lg"
        >
          🛒 Cart
          <span
            v-if="cart.length"
            class="absolute -top-2 -right-2 bg-red-600 text-xs w-5 h-5 flex items-center justify-center rounded-full"
          >
            {{ cart.length }}
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
            class="w-full px-4 py-2 rounded-lg bg-gray-100"
          />

          <div class="space-y-2">
            <button
              v-for="cat in categories"
              :key="cat"
              @click="selectedCategory = cat"
              class="w-full px-3 py-2 rounded-lg text-left"
              :class="selectedCategory === cat
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100'"
            >
              {{ cat }}
            </button>
          </div>
        </div>
      </aside>

      <!-- ================= Products ================= -->
      <main class="flex-1">
        <h2 class="text-2xl font-bold mb-2">{{ selectedCategory }}</h2>
        <p class="text-gray-500 mb-6">{{ products.length }} products found</p>

        <div
          v-if="products.length"
          class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
        >
          <div
            v-for="product in products"
            :key="product.id"
            class="bg-white rounded-xl shadow hover:shadow-xl transition overflow-hidden"
          >

            <!-- Image -->
            <div class="relative h-48 bg-gray-100">
              <img
  :src="API_BASE_URL + product.image_url"
  class="w-full h-full object-cover"
/>
              <span
                class="absolute top-2 left-2 px-2 py-1 text-xs rounded text-white"
                :class="product.is_in_stock ? 'bg-green-600' : 'bg-red-600'"
              >
                {{ product.is_in_stock ? 'In Stock' : 'Out of Stock' }}
              </span>
            </div>

            <!-- Content -->
            <div class="p-4 space-y-2">
              <h3 class="font-semibold">{{ product.product_name_en }}</h3>
              <p class="text-sm text-gray-500 line-clamp-2">
                {{ product.short_description_en }}
              </p>

              <div class="flex items-center gap-2">
                <span class="text-lg font-bold text-blue-600">
                  ৳ {{ product.discount_price }}
                </span>
                <span
                  v-if="product.discount_price < product.price"
                  class="text-sm line-through text-gray-400"
                >
                  ৳ {{ product.price }}
                </span>
              </div>

              <button
                @click="addToCart(product)"
                :disabled="!product.is_in_stock"
                class="w-full py-2 rounded-lg text-white"
                :class="product.is_in_stock
                  ? 'bg-blue-600 hover:bg-blue-700'
                  : 'bg-gray-400 cursor-not-allowed'"
              >
                Add to Cart
              </button>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-20 text-gray-500">
          No products found
        </div>
      </main>
    </div>

    <!-- ================= Cart Modal ================= -->
    <div
      v-if="cartOpen"
      class="fixed inset-0 bg-black/40 flex justify-end z-50"
    >
      <div class="w-full max-w-md bg-white h-full p-6 flex flex-col">

        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">Your Cart</h2>
          <button @click="cartOpen = false">✖</button>
        </div>

        <div class="flex-1 overflow-y-auto space-y-4">
          <div
            v-for="item in cart"
            :key="item.id"
            class="flex gap-3 border-b pb-3"
          >
            <img
              :src="`/${item.image}`"
              class="w-16 h-16 object-cover rounded"
            />
            <div class="flex-1">
              <h4 class="font-semibold">{{ item.product_name_en }}</h4>
              <p class="text-sm text-gray-500">
                ৳ {{ item.discount_price }} × {{ item.qty }}
              </p>

              <div class="flex items-center gap-2 mt-1">
                <button @click="item.qty--" class="px-2 bg-gray-200">-</button>
                <span>{{ item.qty }}</span>
                <button @click="item.qty++" class="px-2 bg-gray-200">+</button>
                <button
                  @click="removeFromCart(item.id)"
                  class="ml-auto text-red-600 text-sm"
                >
                  Remove
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="border-t pt-4">
          <p class="font-bold text-lg">
            Total: ৳ {{ cartTotal }}
          </p>
          <button class="w-full mt-3 py-3 bg-blue-600 text-white rounded-lg">
            Checkout
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from "vue";
import axios from "axios";
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

/* ================= State ================= */
const products = ref([]);
const searchQuery = ref("");
const selectedCategory = ref("All Products");
const cart = ref([]);
const cartOpen = ref(false);

const categories = [
  "All Products",
  "Electronics",
  "Fashion",
  "Home",
  "Sports",
];

/* ================= API ================= */
const fetchProducts = async () => {
  const res = await axios.get("/api/products-landing-search", {
    params: {
      q: searchQuery.value || null,
      category:
        selectedCategory.value === "All Products"
          ? null
          : selectedCategory.value,
    },
  });
  products.value = res.data.data.data;
};

/* ================= Cart ================= */
const addToCart = (product) => {
  const existing = cart.value.find(p => p.id === product.id);
  if (existing) {
    existing.qty++;
  } else {
    cart.value.push({ ...product, qty: 1 });
  }
};

const removeFromCart = (id) => {
  cart.value = cart.value.filter(p => p.id !== id);
};

const cartTotal = computed(() =>
  cart.value.reduce(
    (sum, p) => sum + Number(p.discount_price) * p.qty,
    0
  )
);

/* ================= Watch ================= */
watch([searchQuery, selectedCategory], fetchProducts);

/* ================= Init ================= */
onMounted(fetchProducts);
</script>
