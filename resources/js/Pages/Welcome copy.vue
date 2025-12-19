<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">

    <!-- ================= Header ================= -->
    <header class="bg-white shadow sticky top-0 z-40">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-600">ShopEasy</h1>

        <button
          @click="cartOpen = true"
          class="relative bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
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
    <div
      v-if="detailsOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      @click.self="detailsOpen = false"
    >
      <div class="bg-white w-full max-w-4xl rounded-xl relative max-h-[90vh] overflow-auto">

        <button
          @click="detailsOpen = false"
          class="absolute top-3 right-3 text-xl z-10 w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-full"
        >
          ✖
        </button>

        <div class="grid md:grid-cols-2 gap-6 p-6">

          <!-- Images -->
          <div>
            <img
              :src="getImageUrl(selectedProduct.image_url)"
              :alt="selectedProduct.product_name_en"
              class="w-full h-72 object-cover rounded-lg"
              @error="handleImageError"
            />

            <div class="flex gap-2 mt-3 overflow-x-auto">
              <img
                v-for="(img, i) in selectedProduct.images_urls || []"
                :key="i"
                :src="getImageUrl(img)"
                class="w-20 h-20 object-cover rounded border hover:border-blue-500 cursor-pointer"
                @click="selectedProduct.image_url = img"
              />
            </div>
          </div>

          <!-- Details -->
          <div class="space-y-4">
            <h2 class="text-2xl font-bold">
              {{ selectedProduct.product_name_en }}
            </h2>

            <p class="text-gray-500">
              Category:
              <span class="font-medium text-gray-800">
                {{ selectedProduct.category?.category_name_en || 'Uncategorized' }}
              </span>
            </p>

            <p class="text-gray-600 leading-relaxed">
              {{ selectedProduct.description_en }}
            </p>

            <div class="flex items-center gap-3">
              <span class="text-2xl font-bold text-blue-600">
                ৳ {{ formatPrice(selectedProduct.discount_price) }}
              </span>
              <span
                v-if="selectedProduct.discount_price < selectedProduct.price"
                class="line-through text-gray-400 text-lg"
              >
                ৳ {{ formatPrice(selectedProduct.price) }}
              </span>
            </div>

            <p
              class="text-sm font-medium py-1 px-3 rounded-full inline-block"
              :class="selectedProduct.is_in_stock ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100'"
            >
              {{ selectedProduct.is_in_stock ? 'In Stock' : 'Out of Stock' }}
            </p>

            <button
              @click="addToCart(selectedProduct)"
              :disabled="!selectedProduct.is_in_stock"
              class="w-full py-3 rounded-lg text-white font-medium transition-colors"
              :class="selectedProduct.is_in_stock
                ? 'bg-blue-600 hover:bg-blue-700'
                : 'bg-gray-400 cursor-not-allowed'"
            >
              Add to Cart
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ================= Cart Modal ================= -->
    <!-- ================= Cart Modal - Centered Version ================= -->
<div
  v-if="cartOpen"
  class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
  @click.self="cartOpen = false"
>
  <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl max-h-[90vh] flex flex-col">
    
    <!-- Header -->
    <div class="flex justify-between items-center p-6 border-b">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Your Shopping Cart</h2>
        <p class="text-sm text-gray-500 mt-1">
          {{ cart.length }} item{{ cart.length !== 1 ? 's' : '' }} in cart
        </p>
      </div>
      <button 
        @click="cartOpen = false" 
        class="p-2 hover:bg-gray-100 rounded-full transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Cart Items -->
    <div class="flex-1 overflow-y-auto p-6">
      <div v-if="cart.length" class="space-y-4">
        <div
          v-for="item in cart"
          :key="item.id"
          class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg"
        >
          <img
            :src="getImageUrl(item.image_url)"
            :alt="item.product_name_en"
            class="w-16 h-16 object-cover rounded-lg"
            @error="handleImageError"
          />
          
          <div class="flex-1 min-w-0">
            <h4 class="font-semibold text-gray-800 truncate">{{ item.product_name_en }}</h4>
            <p class="text-sm text-gray-500 mt-1">
              Unit Price: ৳ {{ formatPrice(item.discount_price) }}
            </p>
            <div class="flex items-center justify-between mt-2">
              <div class="flex items-center gap-2">
                <button 
                  @click="updateQuantity(item.id, item.qty - 1)" 
                  :disabled="item.qty <= 1"
                  class="w-7 h-7 flex items-center justify-center bg-gray-200 hover:bg-gray-300 disabled:opacity-40 rounded transition-colors"
                >
                  <span class="text-sm">−</span>
                </button>
                <span class="w-8 text-center font-medium">{{ item.qty }}</span>
                <button 
                  @click="updateQuantity(item.id, item.qty + 1)"
                  class="w-7 h-7 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded transition-colors"
                >
                  <span class="text-sm">+</span>
                </button>
              </div>
              
              <div class="text-right">
                <p class="font-bold text-blue-600">
                  ৳ {{ formatPrice(item.discount_price * item.qty) }}
                </p>
                <button
                  @click="removeFromCart(item.id)"
                  class="text-sm text-red-500 hover:text-red-700 transition-colors mt-1"
                >
                  Remove
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-12">
        <div class="w-20 h-20 mx-auto mb-4 text-gray-300">
          <svg fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <p class="text-gray-500 text-lg font-medium">Your cart is empty</p>
        <p class="text-gray-400 text-sm mt-2">Add some products to get started</p>
        <button 
          @click="cartOpen = false"
          class="mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          Browse Products
        </button>
      </div>
    </div>

    <!-- Order Summary - Only show if cart has items -->
    <div v-if="cart.length" class="border-t">
      <!-- Customer Information -->
      <div class="p-6">
        <h3 class="font-bold mb-4 text-lg text-gray-800">Customer Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium mb-2">Full Name *</label>
            <input
              type="text"
              v-model="customerInfo.name"
              placeholder="Your full name"
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
          </div>

          <!-- Mobile Number -->
          <div>
            <label class="block text-sm font-medium mb-2">Mobile Number *</label>
            <input
              type="tel"
              v-model="customerInfo.mobile"
              placeholder="01XXXXXXXXX"
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p v-if="errors.mobile" class="text-red-500 text-sm mt-1">{{ errors.mobile }}</p>
          </div>
        </div>

        <!-- Delivery Location -->
        <div class="mt-4">
          <label class="block text-sm font-medium mb-2">Delivery Location *</label>
          <div class="flex flex-wrap gap-4">
            <label class="inline-flex items-center cursor-pointer bg-gray-50 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
              <input
                type="radio"
                v-model="customerInfo.location"
                value="dhaka"
                class="text-blue-600"
              />
              <span class="ml-3">Inside Dhaka (৳ 50)</span>
            </label>
            <label class="inline-flex items-center cursor-pointer bg-gray-50 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
              <input
                type="radio"
                v-model="customerInfo.location"
                value="outside-dhaka"
                class="text-blue-600"
              />
              <span class="ml-3">Outside Dhaka (৳ 100)</span>
            </label>
          </div>
          <p v-if="errors.location" class="text-red-500 text-sm mt-1">{{ errors.location }}</p>
        </div>

        <!-- Delivery Address -->
        <div class="mt-4">
          <label class="block text-sm font-medium mb-2">Delivery Address *</label>
          <textarea
            v-model="customerInfo.address"
            rows="2"
            placeholder="House #, Road #, Area, City"
            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
          ></textarea>
          <p v-if="errors.address" class="text-red-500 text-sm mt-1">{{ errors.address }}</p>
        </div>

        <!-- Notes -->
        <div class="mt-4">
          <label class="block text-sm font-medium mb-2">Order Notes (Optional)</label>
          <textarea
            v-model="customerInfo.notes"
            rows="2"
            placeholder="Special instructions for delivery"
            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
          ></textarea>
        </div>
      </div>

      <!-- Price Summary -->
      <div class="bg-gray-50 p-6 border-t">
        <h3 class="font-bold mb-4 text-lg text-gray-800">Order Summary</h3>
        
        <div class="space-y-3">
          <div class="flex justify-between">
            <span class="text-gray-600">Subtotal ({{ cart.length }} items)</span>
            <span class="font-medium">৳ {{ formatPrice(cartTotal) }}</span>
          </div>
          
          <div class="flex justify-between">
            <span class="text-gray-600">Delivery Charge</span>
            <span v-if="customerInfo.location === 'dhaka'" class="font-medium">৳ 50</span>
            <span v-else-if="customerInfo.location === 'outside-dhaka'" class="font-medium">৳ 100</span>
            <span v-else class="text-gray-400">Select location</span>
          </div>
          
          <div class="flex justify-between text-lg font-bold pt-3 border-t">
            <span>Total Amount</span>
            <span class="text-blue-600">৳ {{ formatPrice(totalWithDelivery) }}</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 space-y-3">
          <button 
            @click="proceedToCheckout"
            :disabled="!isFormValid"
            class="w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl font-medium"
          >
            Place Order • ৳ {{ formatPrice(totalWithDelivery) }}
          </button>
          
          <div class="flex gap-3">
            <button 
              @click="cartOpen = false"
              class="flex-1 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Continue Shopping
            </button>
            <button 
              @click="cart = []"
              v-if="cart.length"
              class="flex-1 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition-colors"
            >
              Clear Cart
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from "vue";
import axios from "axios";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:3000';

/* ================= State ================= */
const products = ref([]);
const searchQuery = ref("");
const selectedCategory = ref("All Products");

const cart = ref(JSON.parse(localStorage.getItem('cart')) || []);
const cartOpen = ref(false);

const detailsOpen = ref(false);
const selectedProduct = ref({});

const categories = [
  "All Products",
  "Electronics",
  "Fashion",
  "Home",
  "Sports",
];

const customerInfo = ref({
  name: '',
  mobile: '',
  location: '',
  address: '',
  notes: ''
});

const errors = ref({});

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

const cartTotal = computed(() =>
  cart.value.reduce(
    (sum, p) => sum + (Number(p.discount_price) || 0) * (p.qty || 1),
    0
  )
);

const totalWithDelivery = computed(() => {
  const total = cartTotal.value;
  if (customerInfo.value.location === 'dhaka') return total + 50;
  if (customerInfo.value.location === 'outside-dhaka') return total + 100;
  return total;
});

const isFormValid = computed(() => {
  errors.value = {};
  
  if (!customerInfo.value.name?.trim()) {
    errors.value.name = 'Name is required';
  }
  
  if (!customerInfo.value.mobile?.trim()) {
    errors.value.mobile = 'Mobile number is required';
  } else if (!/^01[3-9]\d{8}$/.test(customerInfo.value.mobile)) {
    errors.value.mobile = 'Enter a valid Bangladeshi mobile number';
  }
  
  if (!customerInfo.value.address?.trim()) {
    errors.value.address = 'Address is required';
  }
  
  if (!customerInfo.value.location) {
    errors.value.location = 'Please select delivery location';
  }
  
  return Object.keys(errors.value).length === 0;
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
  
  const existing = cart.value.find(p => p.id === product.id);
  if (existing) {
    existing.qty++;
  } else {
    cart.value.push({ 
      ...product, 
      qty: 1 
    });
  }
  
  saveCartToLocalStorage();
  
  // Show notification
  if (!cartOpen.value) {
    // You can add a toast notification here
    console.log('Added to cart:', product.product_name_en);
  }
};

const updateQuantity = (productId, newQty) => {
  if (newQty < 1) {
    removeFromCart(productId);
    return;
  }
  
  const item = cart.value.find(p => p.id === productId);
  if (item) {
    item.qty = newQty;
    saveCartToLocalStorage();
  }
};

const removeFromCart = (productId) => {
  cart.value = cart.value.filter(p => p.id !== productId);
  saveCartToLocalStorage();
};

const saveCartToLocalStorage = () => {
  localStorage.setItem('cart', JSON.stringify(cart.value));
};

const proceedToCheckout = () => {
  if (!isFormValid.value) return;
  
  // Prepare order data
  const orderData = {
    customer: customerInfo.value,
    items: cart.value,
    total: totalWithDelivery.value,
    subtotal: cartTotal.value,
    deliveryCharge: customerInfo.value.location === 'dhaka' ? 50 : 100,
    timestamp: new Date().toISOString()
  };
  
  console.log('Order placed:', orderData);
  
  // Here you would typically:
  // 1. Send order to your backend API
  // 2. Show success message
  // 3. Clear cart
  // 4. Redirect to thank you page
  
  // For now, just show an alert and clear cart
  alert(`Order placed successfully!\nTotal: ৳ ${formatPrice(totalWithDelivery.value)}`);
  
  // Clear cart
  cart.value = [];
  saveCartToLocalStorage();
  
  // Close cart modal
  cartOpen.value = false;
  
  // Reset customer info
  customerInfo.value = {
    name: '',
    mobile: '',
    location: '',
    address: '',
    notes: ''
  };
};

/* ================= Watch ================= */
watch([searchQuery, selectedCategory], fetchProducts);

// Watch cart changes for localStorage
watch(cart, saveCartToLocalStorage, { deep: true });

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