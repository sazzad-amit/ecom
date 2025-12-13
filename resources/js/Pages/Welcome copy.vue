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
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              />
              <div class="absolute top-3 right-3 bg-blue-600 text-white px-3 py-1 rounded-full text-xs shadow-lg">
                New
              </div>
            </div>

            <div class="p-4">
              <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">
                {{ product.name }}
              </h3>

              <div class="flex items-center justify-between">
                <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                  ${{ product.price }}
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
import { ref, computed } from "vue";

/* ------------------------------
   Data
--------------------------------*/
const allProducts = ref([
  { id: 1, name: "Wireless Headphones", price: 129, category: "Electronics", image: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400" },
  { id: 2, name: "Premium Watch", price: 249, category: "Fashion", image: "https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=400" },
  { id: 3, name: "Running Shoes", price: 89, category: "Sports", image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400" },
  { id: 4, name: "Smart Speaker", price: 179, category: "Electronics", image: "https://images.unsplash.com/photo-1589492477829-5e65395b66cc?w=400" },
  { id: 5, name: "Leather Jacket", price: 299, category: "Fashion", image: "https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400" },
  { id: 6, name: "Coffee Maker", price: 149, category: "Home", image: "https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?w=400" },
  { id: 7, name: "Yoga Mat", price: 39, category: "Sports", image: "https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=400" },
  { id: 8, name: "Desk Lamp", price: 59, category: "Home", image: "https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400" },
  { id: 9, name: "Bluetooth Speaker", price: 99, category: "Electronics", image: "https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400" },
  { id: 10, name: "Sunglasses", price: 119, category: "Fashion", image: "https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400" },
  { id: 11, name: "Gaming Mouse", price: 79, category: "Electronics", image: "https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400" },
  { id: 12, name: "Backpack", price: 69, category: "Fashion", image: "https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400" },
  { id: 13, name: "Water Bottle", price: 25, category: "Sports", image: "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400" },
  { id: 14, name: "Wall Clock", price: 45, category: "Home", image: "https://images.unsplash.com/photo-1563861826100-9cb868fdbe1c?w=400" },
  { id: 15, name: "Keyboard", price: 129, category: "Electronics", image: "https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400" },
  { id: 16, name: "Sneakers", price: 95, category: "Sports", image: "https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400" },
  { id: 17, name: "Throw Pillow", price: 35, category: "Home", image: "https://images.unsplash.com/photo-1584100936595-c0654b55a2e2?w=400" },
  { id: 18, name: "T-Shirt", price: 29, category: "Fashion", image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400" },
  { id: 19, name: "Dumbbell Set", price: 159, category: "Sports", image: "https://images.unsplash.com/photo-1638805982835-e6c8b1a3ecf1?w=400" },
  { id: 20, name: "Table Lamp", price: 79, category: "Home", image: "https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=400" },
  { id: 21, name: "Wireless Earbuds", price: 159, category: "Electronics", image: "https://images.unsplash.com/photo-1590658165737-15a047b7a9d8?w=400" },
  { id: 22, name: "Baseball Cap", price: 25, category: "Fashion", image: "https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400" },
  { id: 23, name: "Yoga Block", price: 19, category: "Sports", image: "https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=400" },
  { id: 24, name: "Plant Pot", price: 29, category: "Home", image: "https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=400" },
  { id: 25, name: "Webcam", price: 89, category: "Electronics", image: "https://images.unsplash.com/photo-1587826080692-f439cd0b70da?w=400" }
]);

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
const searchQuery = ref("");
const selectedCategory = ref("All Products");
const mobileFilterOpen = ref(false);

/* ------------------------------
   Computed
--------------------------------*/
const filteredProducts = computed(() => {
  return allProducts.value.filter((p) => {
    const matchSearch = p.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchCategory =
      selectedCategory.value === "All Products" || p.category === selectedCategory.value;

    return matchSearch && matchCategory;
  });
});

/* ------------------------------
   Methods
--------------------------------*/
function selectCategory(cat) {
  selectedCategory.value = cat;
  mobileFilterOpen.value = false;
}
</script>
