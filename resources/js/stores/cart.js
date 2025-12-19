import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCartStore = defineStore('cart', () => {
  const cart = ref(JSON.parse(localStorage.getItem('cart')) || []);

  const cartTotal = computed(() =>
    cart.value.reduce(
      (sum, p) => sum + (Number(p.discount_price) || 0) * (p.qty || 1),
      0
    )
  );

  const addToCart = (product) => {
    const existing = cart.value.find(p => p.id === product.id);
    if (existing) {
      existing.qty++;
    } else {
      cart.value.push({ 
        ...product, 
        qty: 1 
      });
    }
    saveToLocalStorage();
  };

  const updateQuantity = (productId, newQty) => {
    if (newQty < 1) {
      removeFromCart(productId);
      return;
    }
    
    const item = cart.value.find(p => p.id === productId);
    if (item) {
      item.qty = newQty;
      saveToLocalStorage();
    }
  };

  const removeFromCart = (productId) => {
    cart.value = cart.value.filter(p => p.id !== productId);
    saveToLocalStorage();
  };

  const clearCart = () => {
    cart.value = [];
    saveToLocalStorage();
  };

  const saveToLocalStorage = () => {
    localStorage.setItem('cart', JSON.stringify(cart.value));
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