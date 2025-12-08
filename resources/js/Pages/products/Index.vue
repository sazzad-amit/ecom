<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Products List</h1>
    </div>

    <!-- Search and Create -->
     <div class="flex justify-between items-center mb-4">
        <input
          v-model="search"
          @input="fetchData"
          type="text"
          placeholder="Search products..."
          class="border p-2 rounded w-1/3"
        />
        <button
          @click="goToCreate"
          class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
          + Create Product
        </button>
    </div>

    <table class="table-auto w-full border">
      <thead>
        <tr class="bg-gray-100">
          <th class="border px-4 py-2">ID</th>
          <th class="border px-4 py-2">Name</th>
          <th class="border px-4 py-2">Price</th>
          <th class="border px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products.data" :key="product.id">
          <td class="border px-4 py-2">{{ product.id }}</td>
          <td class="border px-4 py-2">{{ product.product_name_en }}</td>
          <td class="border px-4 py-2">{{ product.price }}</td>
          <td class="border px-4 py-2 space-x-2">
            <button class="px-2 py-1 bg-blue-600 text-white rounded" @click="editProduct(product.id)">Edit</button>
            <button class="px-2 py-1 bg-red-600 text-white rounded" @click="deleteProduct(product.id)">Delete</button>
            <button class="px-2 py-1 bg-yellow-600 text-white rounded" @click="detailProduct(product)">Details</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4 flex space-x-2">
      <button
        v-if="products.prev_page_url"
        @click="goToPage(products.current_page - 1)"
        class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400"
      >Previous</button>

      <span class="px-3 py-1">Page {{ products.current_page }} of {{ products.last_page }}</span>

      <button
        v-if="products.next_page_url"
        @click="goToPage(products.current_page + 1)"
        class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400"
      >Next</button>
    </div>

    <!-- Product Details Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white p-6 rounded w-1/2 overflow-y-auto max-h-[80vh]">
    <h2 class="text-xl font-bold mb-4">Product Details</h2>
    <p><strong>ID:</strong> {{ selectedProduct.id }}</p>
    <p><strong>Name (EN):</strong> {{ selectedProduct.product_name_en }}</p>
    <p><strong>Name (BN):</strong> {{ selectedProduct.product_name_bn }}</p>
    <p><strong>Price:</strong> {{ selectedProduct.price }}</p>
    <p><strong>Discount Price:</strong> {{ selectedProduct.discount_price }}</p>
    <p><strong>Quantity:</strong> {{ selectedProduct.quantity }}</p>
    <p><strong>Status:</strong> {{ selectedProduct.status }}</p>
    <p><strong>Description (EN):</strong> {{ selectedProduct.description_en }}</p>
    <p><strong>Description (BN):</strong> {{ selectedProduct.description_bn }}</p>

    <!-- Product Images -->
    <div v-if="selectedProduct.images && selectedProduct.images.length" class="mt-4">
      <h3 class="font-bold mb-2">Images:</h3>
      <div class="flex flex-wrap gap-2">
        <img
          v-for="(img, index) in parsedImages"
          :key="index"
          :src="img"
          class="w-24 h-24 object-cover rounded border"
        />
      </div>
    </div>


    <div class="mt-4 text-right">
      <button @click="closeModal" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600">Close</button>
    </div>
  </div>
</div>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3'
export default {
  props: {
    products: Object,
    filters: Object,
  },
  data() {
    return {
      search: this.filters.search || '',
      showModal: false,
      selectedProduct: null,
    }
  },
  methods: {
    goToCreate() {
      router.get('/products/create');
    },
    fetchData() {
      router.get('/products', { search: this.search }, { preserveState: true, replace: true })
    },
    goToPage(page) {
      router.get('/products', { search: this.search, page }, { preserveState: true })
    },
    editProduct(id) {
      router.get(`/products/${id}/edit`)
    },
    deleteProduct(id) {
      if (confirm('Are you sure?')) {
        router.delete(`/products/${id}`)
      }
    },
    detailProduct(product) {
      this.selectedProduct = product;
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.selectedProduct = null;
    },
  },
}
</script>

<style scoped>
</style>