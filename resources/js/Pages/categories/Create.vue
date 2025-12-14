<template>
  <AppLayout>
    <div class="p-6 max-w-4xl mx-auto">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500"></div>
        <p class="mt-3 text-gray-600">Loading category data...</p>
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">
            {{ category ? 'Edit Category' : 'Create New Category' }}
          </h1>
          <p class="mt-2 text-gray-600">
            {{ category ? 'Update category details below' : 'Fill in the details to create a new category' }}
          </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="bg-white shadow-xl rounded-2xl p-6 md:p-8">
          <!-- Basic Information -->
          <div class="mb-10">
            <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-4 border-b border-gray-200 flex items-center">
              <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Basic Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Category Name (EN) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Category Name (English) *
                  <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.category_name_en"
                  type="text"
                  required
                  :class="[
                    'w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                    form.errors.category_name_en ? 'border-red-300 bg-red-50' : 'border-gray-300'
                  ]"
                  placeholder="e.g., Electronics"
                />
                <div v-if="form.errors.category_name_en" class="mt-2 text-sm text-red-600 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  {{ form.errors.category_name_en }}
                </div>
              </div>

              <!-- Category Name (BN) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Category Name (বাংলা)
                </label>
                <input
                  v-model="form.category_name_bn"
                  type="text"
                  :class="[
                    'w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                    form.errors.category_name_bn ? 'border-red-300 bg-red-50' : 'border-gray-300'
                  ]"
                  placeholder="যেমন: ইলেকট্রনিক্স"
                />
                <div v-if="form.errors.category_name_bn" class="mt-2 text-sm text-red-600 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  {{ form.errors.category_name_bn }}
                </div>
              </div>

              <!-- Parent Category -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Parent Category
                </label>
                <select
                  v-model="form.parent_id"
                  :class="[
                    'w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none',
                    form.errors.parent_id ? 'border-red-300 bg-red-50' : 'border-gray-300'
                  ]"
                >
                  <option value="">Select a parent category (optional)</option>
                  <option
                    v-for="parentCat in parentCategories"
                    :key="parentCat.id"
                    :value="parentCat.id"
                    :disabled="category && category.id === parentCat.id"
                  >
                    {{ parentCat.category_name_en }}
                    {{ parentCat.category_name_bn ? `(${parentCat.category_name_bn})` : '' }}
                  </option>
                </select>
                <div v-if="form.errors.parent_id" class="mt-2 text-sm text-red-600 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  {{ form.errors.parent_id }}
                </div>
                <p class="mt-2 text-sm text-gray-500">
                  Leave empty if this is a main category
                </p>
              </div>

              <!-- Status -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Status
                </label>
                <div class="flex items-center space-x-4">
                  <label class="flex items-center">
                    <input
                      type="radio"
                      v-model="form.status"
                      :value="1"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                    />
                    <span class="ml-2 text-gray-700">Active</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      type="radio"
                      v-model="form.status"
                      :value="0"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                    />
                    <span class="ml-2 text-gray-700">Inactive</span>
                  </label>
                </div>
                <p class="mt-2 text-sm text-gray-500">
                  Inactive categories won't be visible in the frontend
                </p>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="pt-8 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-500">
              <span class="text-red-500">*</span> Required fields
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('categories.index')"
                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                :class="[
                  'px-6 py-3 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
                  form.processing
                    ? 'bg-blue-400 cursor-not-allowed text-white'
                    : 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500'
                ]"
              >
                <span v-if="form.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ category ? 'Updating...' : 'Creating...' }}
                </span>
                <span v-else>
                  {{ category ? 'Update Category' : 'Create Category' }}
                </span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  category: {
    type: Object,
    default: null
  },
  categories: {
    type: Array,
    default: () => []
  }
});

const loading = ref(props.category === null ? false : true);
const form = useForm({
  category_name_en: props.category?.category_name_en || '',
  category_name_bn: props.category?.category_name_bn || '',
  parent_id: props.category?.parent_id || '',
  status: props.category?.status ?? 1,
});

const parentCategories = computed(() => {
  if (!props.category) return props.categories;
  return props.categories.filter(cat => cat.id !== props.category.id);
});

onMounted(() => {
  if (props.category) {
    loading.value = false;
  }
});

const submit = () => {
  if (props.category) {
    // Update existing category
    form.put(route('categories.update', props.category.id), {
      preserveScroll: true,
      onSuccess: () => {
        // Success handled by Inertia
      },
      onError: (errors) => {
        console.error('Update failed:', errors);
      }
    });
  } else {
    // Create new category
    form.post(route('categories.store'), {
      preserveScroll: true,
      onSuccess: () => {
        // Success handled by Inertia
      },
      onError: (errors) => {
        console.error('Creation failed:', errors);
      }
    });
  }
};
</script>