import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCartStore = defineStore('cart', () => {
  // Load from localStorage or initialize empty
  const cart = ref(JSON.parse(localStorage.getItem('shopEasyCart')) || []);

  // Computed total
  const cartTotal = computed(() =>
    cart.value.reduce(
      (sum, item) => sum + (Number(item.discount_price) || 0) * (item.qty || 1),
      0
    )
  );

  // Add to cart
  const addToCart = (product) => {
    const existing = cart.value.find(item => item.id === product.id);
    
    if (existing) {
      existing.qty += 1;
    } else {
      cart.value.push({ 
        ...product, 
        qty: 1 
      });
    }
    
    saveToLocalStorage();
  };

  // Update quantity
  const updateQuantity = (productId, newQty) => {
    if (newQty < 1) {
      removeFromCart(productId);
      return;
    }
    
    const item = cart.value.find(item => item.id === productId);
    if (item) {
      item.qty = newQty;
      saveToLocalStorage();
    }
  };

  // Remove item
  const removeFromCart = (productId) => {
    const index = cart.value.findIndex(item => item.id === productId);
    if (index !== -1) {
      cart.value.splice(index, 1);
      saveToLocalStorage();
    }
  };

  // Clear cart
  const clearCart = () => {
    cart.value = [];
    saveToLocalStorage();
  };

  // Save to localStorage
  const saveToLocalStorage = () => {
    localStorage.setItem('shopEasyCart', JSON.stringify(cart.value));
  };

  return {
    cart,
    cartTotal,
    addToCart,
    updateQuantity,
    removeFromCart,
    clearCart,
    saveToLocalStorage
  };
});