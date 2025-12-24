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
      <!-- ================= Sidebar ================= -->
      <aside class="w-64 hidden lg:block" v-if="categories.length">
        <div class="bg-white rounded-xl shadow p-4 space-y-4 sticky top-24">
          <input
            v-model="searchQuery"
            placeholder="Search product..."
            class="w-full px-4 py-2 rounded-lg bg-gray-100 border focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="handleSearch"
          />

          <!-- UNIFIED CATEGORY TREE -->
          <div class="space-y-1 text-sm">
            <TreeNode
              v-for="node in treeStructure"
              :key="node.id"
              :node="node"
              :level="0"
              @select="selectCategory"
            />
          </div>
        </div>
      </aside>

      <!-- ================= Products ================= -->
      <main class="flex-1">
        <h2 class="text-2xl font-bold mb-2">
          {{ selectedTitle }}
        </h2>

        <p class="text-gray-500 mb-6">
          {{ filteredProducts.length }} products found
        </p>

        <div v-if="loading" class="text-center py-20">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
          <p class="mt-2 text-gray-600">Loading products...</p>
        </div>

        <div
          v-else-if="filteredProducts.length"
          class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
        >
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition-shadow cursor-pointer"
            @click="openProductDetails(product)"
          >
            <img
              :src="getImageUrl(product.image_url)"
              :alt="product.product_name_en"
              class="w-full h-40 object-cover rounded"
            />

            <h3 class="mt-2 font-semibold">
              {{ product.product_name_en }}
            </h3>

            <p class="text-sm text-gray-500 line-clamp-2">
              {{ product.short_description_en }}
            </p>

            <div class="mt-2 font-bold text-blue-600">
              ৳ {{ formatPrice(product.discount_price) }}
              <span v-if="product.original_price" class="text-sm text-gray-400 line-through ml-2">
                ৳ {{ formatPrice(product.original_price) }}
              </span>
            </div>

            <div class="mt-3 flex gap-2">
              <button
                @click.stop="addToCart(product)"
                class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition-colors"
              >
                Add to Cart
              </button>

              <button
                @click.stop="openProductDetails(product)"
                class="flex-1 bg-gray-600 text-white py-2 rounded hover:bg-gray-700 transition-colors"
              >
                Details
              </button>
            </div>

          </div>
        </div>

        <div v-else class="text-center py-20 text-gray-400">
          No products found
        </div>
      </main>
    </div>

    <!-- ================= Modals ================= -->
    <CartModal
      v-if="showCartModal"
      @close="showCartModal = false"
    />

    <ProductDetailsModal
      v-if="showProductModal && selectedProduct"
      :product="selectedProduct"
      @close="showProductModal = false"
      @add-to-cart="addToCartFromModal"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import axios from "axios"
import { useCartStore } from "@/stores/cart"
import TreeNode from "@/components/TreeNode.vue"
import CartModal from "@/components/CartModal.vue"
import ProductDetailsModal from "@/components/ProductDetailsModal.vue"

const API_BASE_URL =
  import.meta.env.VITE_API_BASE_URL || "http://localhost:3000"

/* ================= State ================= */
const products = ref([])
const categories = ref([])
const searchQuery = ref("")
const selectedLeafIds = ref([])
const selectedTitle = ref("All Products")
const loading = ref(false)
const showCartModal = ref(false)
const showProductModal = ref(false)
const selectedProduct = ref(null)

const cartStore = useCartStore()

/* ================= Category Helpers ================= */

// id => category
const categoryMap = computed(() => {
  const map = {}
  categories.value.forEach(c => {
    if (c && c.id) {
      map[c.id] = c
    }
  })
  return map
})

// parent => children
const childrenMap = computed(() => {
  const map = {}
  categories.value.forEach(c => {
    if (c && c.parent_id) {
      if (!map[c.parent_id]) map[c.parent_id] = []
      map[c.parent_id].push(c)
    }
  })
  return map
})

// Build unified tree structure
const treeStructure = computed(() => {
  const roots = categories.value.filter(cat => cat && !cat.parent_id)
  
  const buildTree = (cat) => {
    const children = childrenMap.value[cat.id] || []
    return {
      ...cat,
      children: children.map(child => buildTree(child))
    }
  }
  
  return roots.map(root => buildTree(root))
})

// get all leaf ids under any node
const getLeafByParent = (parentId) => {
  const result = []

  const walk = (id) => {
    if (!childrenMap.value[id] || childrenMap.value[id].length === 0) {
      result.push(id)
      return
    }
    childrenMap.value[id].forEach(c => walk(c.id))
  }

  walk(parentId)
  return result
}

/* ================= Actions ================= */

const selectCategory = (cat) => {
  if (!cat || !cat.id) return
  
  selectedLeafIds.value = getLeafByParent(cat.id)
  selectedTitle.value = cat.category_name_en
  fetchProducts()
}

const fetchProducts = async () => {
  loading.value = true
  try {
    const params = {}
    
    if (searchQuery.value.trim()) {
      params.q = searchQuery.value.trim()
    }
    
    if (selectedLeafIds.value.length > 0) {
      params.category_ids = selectedLeafIds.value.join(',')
    }
    
    const res = await axios.get(
      `${API_BASE_URL}/api/products-landing-search`,
      { params }
    )

    products.value = res.data?.data?.data || []
  } catch (e) {
    console.error("Error fetching products:", e)
    products.value = []
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const res = await axios.get(
      `${API_BASE_URL}/api/products-categories-search`
    )
    categories.value = res.data?.data?.data || []
  } catch (e) {
    console.error("Error fetching categories:", e)
    categories.value = []
  }
}

/* ================= Modal Functions ================= */

const openCart = () => {
  showCartModal.value = true
}

const openProductDetails = (product) => {
  selectedProduct.value = product
  showProductModal.value = true
}

const addToCartFromModal = (product) => {
  addToCart(product)
  // Optional: Close modal after adding to cart
  // showProductModal.value = false
}

/* ================= Utils ================= */

const getImageUrl = (path) => {
  if (!path) return "/placeholder.jpg"
  if (path.startsWith("http")) return path
  return `${API_BASE_URL}${path.startsWith('/') ? path : '/' + path}`
}

const formatPrice = (price) =>
  Number(price || 0).toLocaleString("en-BD")

const addToCart = (product) => {
  cartStore.addToCart(product)
  
  // Optional: Show notification or feedback
  // alert(`${product.product_name_en} added to cart!`)
}

const handleSearch = () => {
  fetchProducts()
}

/* ================= Computed ================= */

const filteredProducts = computed(() => {
  if (!searchQuery.value) return products.value

  const query = searchQuery.value.toLowerCase()
  return products.value.filter(p =>
    (p.product_name_en?.toLowerCase().includes(query) ||
    p.short_description_en?.toLowerCase().includes(query))
  )
})

/* ================= Lifecycle ================= */
onMounted(() => {
  fetchCategories()
  fetchProducts()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Modal background blur effect */
.modal-backdrop {
  backdrop-filter: blur(4px);
}
</style>