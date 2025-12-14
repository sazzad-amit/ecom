<!-- resources/js/Pages/Categories/Index.vue -->
<template>
  <AppLayout>
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Category List</h1>
      </div>

      <!-- Search and Create -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <input
          v-model="search"
          @input="fetchData"
          type="text"
          placeholder="Search categories..."
          class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
        />
        <button
          @click="goToCreate"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Create Category
        </button>
      </div>

      <!-- Category Table -->
      <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="category in categories.data" :key="category.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ category.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ category.category_name_en }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <button 
                    @click="editCategory(category.id)" 
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    Edit
                  </button>
                  <button 
                    @click="deleteCategory(category.id)" 
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                  >
                    Delete
                  </button>
                  <button 
                    @click="detailCategory(category)" 
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
          Showing page {{ categories.current_page }} of {{ categories.last_page }}
        </div>
        <div class="flex space-x-2">
          <button
            v-if="categories.prev_page_url"
            @click="goToPage(categories.current_page - 1)"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
          >
            Previous
          </button>
          <button
            v-if="categories.next_page_url"
            @click="goToPage(categories.current_page + 1)"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
          >
            Next
          </button>
        </div>
      </div>

      <!-- Category Details Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
          <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
          <h2 class="text-xl font-bold mb-4">{{ selectedCategory.category_name_en }}</h2>
          <p><strong>ID:</strong> {{ selectedCategory.id }}</p>
          <p><strong>Parent ID:</strong> {{ selectedCategory.parent_id || 'N/A' }}</p>
          <p><strong>Status:</strong> {{ selectedCategory.status ? 'Active' : 'Inactive' }}</p>
          <!-- যদি images থাকে -->
          <div v-if="parsedImages.length" class="mt-4">
            <h3 class="font-semibold mb-2">Images:</h3>
            <div class="flex gap-2">
              <img v-for="(img, index) in parsedImages" :key="index" :src="img" class="w-20 h-20 object-cover rounded-md" />
            </div>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  components: { AppLayout },
  props: {
    categories: Object,
    filters: Object,
  },
  data() {
    return {
      search: this.filters?.search || '',
      showModal: false,
      selectedCategory: null,
    }
  },
  computed: {
    parsedImages() {
      try {
        return this.selectedCategory?.images ? JSON.parse(this.selectedCategory.images) : [];
      } catch(e) {
        return [];
      }
    }
  },
  methods: {
    goToCreate() {
      this.$inertia.get('/categories/create');
    },
    fetchData() {
      this.$inertia.get('/categories', { search: this.search }, { preserveState: true, replace: true });
    },
    goToPage(page) {
      this.$inertia.get('/categories', { search: this.search, page }, { preserveState: true });
    },
    editCategory(id) {
      this.$inertia.get(`/categories/create?id=${id}`);
    },
    deleteCategory(id) {
      if (confirm('Are you sure you want to delete this category?')) {
        this.$inertia.delete(`/categories/${id}`);
      }
    },
    detailCategory(category) {
      this.selectedCategory = category;
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.selectedCategory = null;
    },
  }
}
</script>
