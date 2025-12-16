<template>
  <AppLayout>
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Source List</h1>
      </div>

      <!-- Search and Create -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <input
          v-model="search"
          @input="onSearch"
          type="text"
          placeholder="Search sources..."
          class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
        />
        <button
          @click="goToCreate"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Create Source
        </button>
      </div>

      <!-- Source Table -->
      <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auto ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="source in sources.data" :key="source.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ source.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ source.source_name_en }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ source.source_auto_id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ source.mobile_no || 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="source.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs font-medium rounded-full">
                    {{ source.status ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <button 
                    @click="editSource(source.id)" 
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    Edit
                  </button>
                  <button 
                    @click="deleteSource(source.id)" 
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                  >
                    Delete
                  </button>
                  <button 
                    @click="showSourceDetails(source)" 
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
          Showing {{ sources.from || 0 }} to {{ sources.to || 0 }} of {{ sources.total }} entries
        </div>
        <div class="flex space-x-2">
          <button
            v-if="sources.prev_page_url"
            @click="goToPage(sources.current_page - 1)"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
          >
            Previous
          </button>
          <button
            v-if="sources.next_page_url"
            @click="goToPage(sources.current_page + 1)"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
          >
            Next
          </button>
        </div>
      </div>

      <!-- Source Details Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
          <button @click="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
          <h2 class="text-xl font-bold mb-4">{{ selectedSource.source_name_en }}</h2>
          <div class="space-y-2">
            <p><strong>ID:</strong> {{ selectedSource.id }}</p>
            <p><strong>Auto ID:</strong> {{ selectedSource.source_auto_id }}</p>
            <p><strong>Name (English):</strong> {{ selectedSource.source_name_en }}</p>
            <p v-if="selectedSource.source_name_bn"><strong>Name (Bangla):</strong> {{ selectedSource.source_name_bn }}</p>
            <p><strong>Mobile:</strong> {{ selectedSource.mobile_no || 'N/A' }}</p>
            <p><strong>Status:</strong> 
              <span :class="selectedSource.status ? 'text-green-600' : 'text-red-600'" class="font-semibold">
                {{ selectedSource.status ? 'Active' : 'Inactive' }}
              </span>
            </p>
            <p v-if="selectedSource.details_en"><strong>Details (EN):</strong> {{ selectedSource.details_en }}</p>
            <p v-if="selectedSource.details_bn"><strong>Details (BN):</strong> {{ selectedSource.details_bn }}</p>
            <p><strong>Created By:</strong> {{ selectedSource.created_by }}</p>
            <p><strong>Created At:</strong> {{ formatDate(selectedSource.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { debounce } from 'lodash';

export default {
  components: { AppLayout },
  props: {
    sources: Object,
    filters: Object,
  },
  data() {
    return {
      search: this.filters?.search || '',
      showModal: false,
      selectedSource: null,
    }
  },
  mounted() {
    this.debouncedFetch = debounce(this.fetchData, 500);
  },
  methods: {
    goToCreate() {
      this.$inertia.get('/sources/create');
    },
    onSearch() {
      this.debouncedFetch();
    },
    fetchData() {
      this.$inertia.get('/sources', { search: this.search }, { 
        preserveState: true, 
        replace: true 
      });
    },
    goToPage(page) {
      this.$inertia.get('/sources', { search: this.search, page }, { 
        preserveState: true 
      });
    },
    editSource(id) {
      this.$inertia.get(`/sources/create?id=${id}`);
    },
    async deleteSource(id) {
      if (confirm('Are you sure you want to delete this source?')) {
        await this.$inertia.delete(`/sources/${id}`);
      }
    },
    showSourceDetails(source) {
      this.selectedSource = source;
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.selectedSource = null;
    },
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleString();
    }
  }
}
</script>