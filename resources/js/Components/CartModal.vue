<template>
  <!-- Overlay -->
  <div
    class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
    @click.self="$emit('close')"
  >
    <!-- Modal -->
    <div
      class="bg-white w-full max-w-2xl h-[90vh] rounded-xl shadow-2xl flex flex-col overflow-hidden"
    >

      <!-- ================= Header (Sticky) ================= -->
      <div class="sticky top-0 z-10 bg-white border-b p-5 flex justify-between items-center">
        <div>
          <h2 class="text-xl font-bold">Your Shopping Cart</h2>
          <p class="text-sm text-gray-500">
            {{ cart.length }} item{{ cart.length !== 1 ? 's' : '' }}
          </p>
        </div>
        <button
          @click="$emit('close')"
          class="p-2 rounded-full hover:bg-gray-100 transition-colors"
          aria-label="Close cart"
        >
          ✕
        </button>
      </div>

      <!-- ================= Scroll Area ================= -->
      <div class="flex-1 overflow-y-auto px-5 py-4 space-y-6">

        <!-- Cart Items -->
        <div v-if="cart.length" class="space-y-4">
          <div
            v-for="item in cart"
            :key="item.id"
            class="flex gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <img
              :src="getImageUrl(item.image_url)"
              :alt="item.product_name_en"
              class="w-16 h-16 rounded object-cover"
              @error="handleImageError"
            />

            <div class="flex-1 min-w-0">
              <h4 class="font-semibold truncate">{{ item.product_name_en }}</h4>
              <div class="flex items-center gap-2 text-sm mt-1">
                <p class="text-gray-500 line-through" v-if="item.discount_price < item.price">
                  ৳ {{ formatPrice(item.price) }}
                </p>
                <p class="text-blue-600 font-medium">
                  ৳ {{ formatPrice(item.discount_price) }}
                </p>
              </div>

              <div class="flex justify-between items-center mt-3">
                <div class="flex items-center gap-2">
                  <button
                    @click="updateQuantity(item.id, item.qty - 1)"
                    :disabled="item.qty <= 1"
                    class="w-7 h-7 bg-gray-200 rounded flex items-center justify-center hover:bg-gray-300 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                    aria-label="Decrease quantity"
                  >−</button>

                  <span class="w-8 text-center font-medium">{{ item.qty }}</span>

                  <button
                    @click="updateQuantity(item.id, item.qty + 1)"
                    class="w-7 h-7 bg-gray-200 rounded flex items-center justify-center hover:bg-gray-300 transition-colors"
                    aria-label="Increase quantity"
                  >+</button>
                </div>

                <div class="text-right">
                  <p class="font-bold text-blue-600">
                    ৳ {{ formatPrice(item.qty * item.discount_price) }}
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

        <!-- Empty Cart State -->
        <div v-else class="text-center py-16 text-gray-500">
          <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <p class="text-lg font-medium mb-2">Your cart is empty</p>
          <p class="text-sm">Add some items to get started</p>
        </div>

        <!-- ================= Customer Info ================= -->
        <div v-if="cart.length" class="space-y-4 pt-4">
          <h3 class="font-semibold text-lg">Customer Information</h3>

          <!-- Error Messages -->
          <div v-if="Object.keys(errors).length" class="bg-red-50 border border-red-200 rounded-lg p-3 space-y-1">
            <p v-for="(error, field) in errors" :key="field" class="text-red-600 text-sm">
              {{ error }}
            </p>
          </div>

          <!-- Form Fields -->
          <div class="space-y-3">
            <div>
              <input 
                v-model="customerInfo.name" 
                placeholder="Full Name *"
                :class="['input', { 'border-red-300': errors.name }]"
              />
            </div>
            
            <div>
              <input 
                v-model="customerInfo.mobile" 
                placeholder="Mobile Number *"
                :class="['input', { 'border-red-300': errors.mobile }]"
              />
            </div>

            <div>
              <textarea 
                v-model="customerInfo.address" 
                placeholder="Delivery Address *"
                rows="3"
                :class="['input', { 'border-red-300': errors.address }]"
              ></textarea>
            </div>

            <div>
              <textarea 
                v-model="customerInfo.notes" 
                placeholder="Order Notes (Optional)"
                rows="2"
                class="input"
              ></textarea>
            </div>

            <div class="space-y-2 pt-2">
              <p class="text-sm font-medium text-gray-700 mb-2">Select Delivery Location *</p>
              <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input 
                    type="radio" 
                    value="dhaka" 
                    v-model="customerInfo.location"
                    :class="{ 'border-red-300': errors.location }"
                  />
                  <span>Dhaka (৳50)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input 
                    type="radio" 
                    value="outside-dhaka" 
                    v-model="customerInfo.location"
                    :class="{ 'border-red-300': errors.location }"
                  />
                  <span>Outside Dhaka (৳100)</span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ================= Footer (Sticky Bottom) ================= -->
      <div
        v-if="cart.length"
        class="sticky bottom-0 bg-white border-t p-5 space-y-4"
      >
        <!-- Price Breakdown -->
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-600">Subtotal</span>
            <span>৳ {{ formatPrice(cartTotal) }}</span>
          </div>
          <div class="flex justify-between" v-if="customerInfo.location">
            <span class="text-gray-600">
              Delivery Charge
              <span class="text-xs text-gray-500">
                ({{ customerInfo.location === 'dhaka' ? 'Dhaka' : 'Outside Dhaka' }})
              </span>
            </span>
            <span>৳ {{ deliveryCharge }}</span>
          </div>
          <hr class="my-2">
          <div class="flex justify-between font-semibold text-base">
            <span>Total</span>
            <span class="text-blue-600">
              ৳ {{ formatPrice(totalWithDelivery) }}
            </span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
          <button
            @click="clearCart"
            class="flex-1 py-3 border border-red-500 text-red-600 rounded-lg hover:bg-red-50 transition-colors"
          >
            Clear Cart
          </button>
          <button
            @click="proceedToCheckout"
            :disabled="processing"
            class="flex-1 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2"
          >
            <span v-if="processing">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Processing...
            </span>
            <span v-else>
              Place Order
            </span>
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useCartStore } from '@/stores/cart';
import axios from 'axios'; // Added axios import

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:3000';

const emit = defineEmits(['close', 'proceed-to-checkout']);

/* ================= State ================= */
const cartStore = useCartStore();
const processing = ref(false);

const customerInfo = ref({
  name: '',
  mobile: '',
  location: '',
  address: '',
  notes: ''
});

const errors = ref({});

/* ================= Computed Properties ================= */
const cart = computed(() => cartStore.cart);
const cartTotal = computed(() => cartStore.cartTotal);
const deliveryCharge = computed(() => {
  if (customerInfo.value.location === 'dhaka') return 50;
  if (customerInfo.value.location === 'outside-dhaka') return 100;
  return 0;
});

const totalWithDelivery = computed(() => {
  return cartTotal.value + deliveryCharge.value;
});

/* ================= Methods ================= */
const validateForm = () => {
  errors.value = {};
  
  // Name validation
  if (!customerInfo.value.name?.trim()) {
    errors.value.name = 'Name is required';
  } else if (customerInfo.value.name.trim().length < 2) {
    errors.value.name = 'Name must be at least 2 characters';
  }
  
  // Mobile validation
  if (!customerInfo.value.mobile?.trim()) {
    errors.value.mobile = 'Mobile number is required';
  } else if (!/^01[3-9]\d{8}$/.test(customerInfo.value.mobile.trim())) {
    errors.value.mobile = 'Enter a valid Bangladeshi mobile number (e.g., 01712345678)';
  }
  
  // Address validation
  if (!customerInfo.value.address?.trim()) {
    errors.value.address = 'Address is required';
  } else if (customerInfo.value.address.trim().length < 10) {
    errors.value.address = 'Please provide a more detailed address';
  }
  
  // Location validation
  if (!customerInfo.value.location) {
    errors.value.location = 'Please select delivery location';
  }
  
  return Object.keys(errors.value).length === 0;
};

const getImageUrl = (imagePath) => {
  if (!imagePath) return '/placeholder-image.jpg';
  if (imagePath.startsWith('http')) return imagePath;
  return `${API_BASE_URL}${imagePath.startsWith('/') ? '' : '/'}${imagePath}`;
};

const handleImageError = (event) => {
  event.target.src = '/placeholder-image.jpg';
  event.target.onerror = null; // Prevent infinite loop
};

const formatPrice = (price) => {
  const num = Number(price || 0);
  return num.toLocaleString('en-BD', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  });
};

const updateQuantity = (productId, newQty) => {
  if (newQty < 1) return;
  cartStore.updateQuantity(productId, newQty);
};

const removeFromCart = (productId) => {
  if (confirm('Are you sure you want to remove this item from your cart?')) {
    cartStore.removeFromCart(productId);
  }
};

const clearCart = () => {
  if (cart.value.length === 0) return;
  if (confirm('Are you sure you want to clear your entire cart?')) {
    cartStore.clearCart();
  }
};

const proceedToCheckout = async () => {
  // Validate form first
  if (!validateForm()) {
    // Scroll to error messages
    setTimeout(() => {
      const errorElement = document.querySelector('.bg-red-50');
      if (errorElement) {
        errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }, 100);
    return;
  }
  
  processing.value = true;
  
  try {
    // Prepare order items in the format your API expects
    const orderItems = cart.value.map(item => ({
      product_id: item.id,
      product_name: item.product_name_en,
      quantity: item.qty,
      unit_price: item.discount_price,
      total_price: item.qty * item.discount_price
    }));
    
    const orderData = {
      customer_name: customerInfo.value.name.trim(),
      customer_mobile: customerInfo.value.mobile.trim(),
      customer_address: customerInfo.value.address.trim(),
      delivery_location: customerInfo.value.location,
      order_notes: customerInfo.value.notes?.trim() || '',
      order_items: orderItems,
      subtotal: cartTotal.value,
      delivery_charge: deliveryCharge.value,
      total_amount: totalWithDelivery.value
    };
    
    console.log('Sending order data:', orderData);
    
    // Send to API - Fixed axios call
    const response = await axios.post(`${API_BASE_URL}/api/products-place-order`, orderData);
    
    if (response.data.success) {
      // Show success message
      alert(`Order placed successfully!\nOrder ID: ${response.data.order_id || 'N/A'}\nTotal: ৳ ${formatPrice(totalWithDelivery.value)}\nWe will contact you soon for confirmation.`);
      
      // Clear cart and reset form
      cartStore.clearCart();
      customerInfo.value = {
        name: '',
        mobile: '',
        location: '',
        address: '',
        notes: ''
      };
      errors.value = {};
      
      emit('proceed-to-checkout', response.data);
      emit('close');
    } else {
      throw new Error(response.data.message || 'Failed to place order');
    }
    
  } catch (error) {
    console.error('Checkout error:', error);
    
    // Show appropriate error message
    if (error.response) {
      // Server responded with error status
      const errorMsg = error.response.data?.message || error.response.statusText;
      alert(`Failed to place order: ${errorMsg}`);
    } else if (error.request) {
      // Request made but no response
      alert('Network error. Please check your connection and try again.');
    } else {
      // Something else happened
      alert('Failed to place order. Please try again.');
    }
  } finally {
    processing.value = false;
  }
};

/* ================= Watch ================= */
watch(cart, (newCart) => {
  if (newCart.length === 0) {
    // Reset form when cart becomes empty
    customerInfo.value = {
      name: '',
      mobile: '',
      location: '',
      address: '',
      notes: ''
    };
    errors.value = {};
  }
}, { immediate: true });

// Auto-validate when form changes (debounced)
let validationTimeout;
watch(customerInfo.value, () => {
  if (Object.keys(errors.value).length > 0) {
    clearTimeout(validationTimeout);
    validationTimeout = setTimeout(() => {
      validateForm();
    }, 500);
  }
}, { deep: true });
</script>

<style scoped>
.input {
  @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all;
}

input[type="radio"] {
  @apply w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500;
}
</style>