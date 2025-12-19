<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
      <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg">
            S
          </div>
          <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            ShopEasy
          </span>
        </div>

        <button
          @click="mobileFilterOpen = !mobileFilterOpen"
          class="lg:hidden px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
        >
          Filters
        </button>
      </div>
    </header>

    <!-- Body -->
    <div class="container mx-auto px-4 py-8 flex flex-col lg:flex-row gap-6">

      <!-- Sidebar -->
      <aside :class="['lg:w-64 flex-shrink-0', mobileFilterOpen ? 'block' : 'hidden lg:block']">
        <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">

          <!-- Search -->
          <div class="mb-6">
            <label class="text-sm font-semibold text-gray-700 mb-2 block">Search Products</label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search..."
                class="w-full pl-10 pr-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-blue-500"
              />
              <span class="absolute left-3 top-3.5 text-gray-400">🔍</span>
            </div>
          </div>

          <!-- Categories -->
          <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Categories</h3>
            <div class="space-y-2">
              <button
                v-for="cat in categories"
                :key="cat.name"
                @click="selectCategory(cat.name)"
                :class="[
                  'w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition-all text-left',
                  selectedCategory === cat.name
                    ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-md transform scale-105'
                    : 'bg-gray-50 text-gray-700 hover:bg-gray-100'
                ]"
              >
                <span class="text-2xl">{{ cat.icon }}</span>
                <span class="font-medium">{{ cat.name }}</span>
              </button>
            </div>
          </div>

        </div>
      </aside>

      <!-- Main Content -->
      <main class="flex-1">
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-800">{{ selectedCategory }}</h1>
          <p class="text-gray-600 mt-1">{{ filteredProducts.length }} products found</p>
        </div>

        <!-- Products Grid -->
        <div v-if="filteredProducts.length > 0"
             class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
          
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="group bg-white rounded-2xl shadow-md hover:shadow-2xl overflow-hidden transform hover:-translate-y-2 transition"
          >
            <div class="relative h-48 bg-gray-100 overflow-hidden">
              <img
                :src="product.image"
                :alt="product.product_name_en"
              />
              <div class="absolute top-3 right-3 bg-blue-600 text-white px-3 py-1 rounded-full text-xs shadow-lg">
                New
              </div>
            </div>

            <div class="p-4">
              <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">
                {{ product.product_name_en }}
              </h3>
//{{ product }}/// sample data: { "id": 4, "auto_id": 131225000000004, "product_name_en": "t-shirt", "product_name_bn": "t-shirt", "category_id": 1, "price": "345.00", "discount_price": "342.00", "image": "products/images/1765604280_693cfbb8d0c7a_Capture.PNG", "images": "[\"products\\/images\\/1765604280_693cfbb8d4d61_Capture.PNG\",\"products\\/images\\/1765604280_693cfbb8d5107_test.png\"]", "video": null, "description_en": "weqe", "description_bn": "qwewqe", "short_description_en": "wewqew", "short_description_bn": "ewqe", "calculation": "qweqwe", "is_in_stock": true, "seller_details": "wqewqe", "mobile_no": "86879678", "quantity": 3, "status": 1, "created_by": 3, "updated_by": null, "created_at": "2025-12-13T05:38:00.000000Z", "updated_at": "2025-12-13T05:38:00.000000Z" }
BDT 345.00//
              <div class="flex items-center justify-between">
                <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                  BDT {{ product.price }} // Discount: BDt{{ product.discount_price }}  
                </span>
              </div>

              <button
                class="mt-3 w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2.5 rounded-xl hover:shadow-lg transition font-medium"
              >
                Add to Cart
              </button>
            </div>

          </div>

        </div>

        <!-- No Products -->
        <div v-else class="text-center py-20">
          <div class="text-6xl mb-4">🔍</div>
          <h3 class="text-2xl font-bold text-gray-700 mb-2">No products found</h3>
          <p class="text-gray-500">Try adjusting your search or filters</p>
        </div>

      </main>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import axios from "axios";

/* ------------------------------
   Categories
--------------------------------*/
const categories = [
  { name: "All Products", icon: "🛍️" },
  { name: "Electronics", icon: "📱" },
  { name: "Fashion", icon: "👕" },
  { name: "Home", icon: "🏠" },
  { name: "Sports", icon: "⚽" },
];

/* ------------------------------
   State
--------------------------------*/
const products = ref([]);
const searchQuery = ref("");
const selectedCategory = ref("All Products");
const mobileFilterOpen = ref(false);
const loading = ref(false);

/* ------------------------------
   API Call
--------------------------------*/
const fetchProducts = async () => {
  loading.value = true;

  try {
    const response = await axios.get("/api/products-landing-search", {
      params: {
        q: searchQuery.value || null,
        category:
          selectedCategory.value === "All Products"
            ? null
            : selectedCategory.value,
      },
    });

    products.value = response.data.data.data;
  } catch (error) {
    console.error("Product fetch failed", error);
  } finally {
    loading.value = false;
  }
};

/* ------------------------------
   Computed
--------------------------------*/
const filteredProducts = computed(() => products.value);

/* ------------------------------
   Watch (Live Search + Filter)
--------------------------------*/
watch([searchQuery, selectedCategory], () => {
  fetchProducts();
});

/* ------------------------------
   Methods
--------------------------------*/
function selectCategory(cat) {
  selectedCategory.value = cat;
  mobileFilterOpen.value = false;
}

/* ------------------------------
   On Load
--------------------------------*/
onMounted(() => {
  fetchProducts();
});
</script>
