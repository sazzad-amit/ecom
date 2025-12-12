<!-- resources/js/Pages/Products/Index.vue -->
<template>
  <AppLayout>
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Products List</h1>
      </div>

      <!-- Search and Create -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <input
          v-model="search"
          @input="fetchData"
          type="text"
          placeholder="Search products..."
          class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
        />
        <button
          @click="goToCreate"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Create Product
        </button>
      </div>

      <!-- Products Table -->
      <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ product.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.product_name_en }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ product.price }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <button 
                    @click="editProduct(product.id)" 
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    Edit
                  </button>
                  <button 
                    @click="deleteProduct(product.id)" 
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                  >
                    Delete
                  </button>
                  <button 
                    @click="detailProduct(product)" 
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors"
                  >
                    Details
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700">
          Showing page {{ products.current_page }} of {{ products.last_page }}
        </div>
        <div class="flex space-x-2">
          <button
            v-if="products.prev_page_url"
            @click="goToPage(products.current_page - 1)"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
          >
            Previous
          </button>
          <button
            v-if="products.next_page_url"
            @click="goToPage(products.current_page + 1)"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
          >
            Next
          </button>
        </div>
      </div>

      <!-- Product Details Modal -->
      <!-- Keep your existing modal code here -->
      <!-- ... -->
      
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  components: {
    AppLayout
  },
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
  computed: {
    parsedImages() {
      try {
        return this.selectedProduct.images ? JSON.parse(this.selectedProduct.images) : [];
      } catch(e) {
        return [];
      }
    }
  },
  methods: {
    goToCreate() {
      this.$inertia.get('/products/create');
    },
    fetchData() {
      this.$inertia.get('/products', { search: this.search }, { preserveState: true, replace: true })
    },
    goToPage(page) {
      this.$inertia.get('/products', { search: this.search, page }, { preserveState: true })
    },
    editProduct(id) {
      this.$inertia.get(`/products/create?id=${id}`)
    },
    deleteProduct(id) {
      if (confirm('Are you sure you want to delete this product?')) {
        this.$inertia.delete(`/products/${id}`)
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