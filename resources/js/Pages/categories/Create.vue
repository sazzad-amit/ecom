<template>
  <AppLayout>
    <div class="p-6 max-w-3xl mx-auto">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        <p class="mt-4 text-gray-500">Loading...</p>
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Header -->
        <div class="mb-8 text-center">
          <h1 class="text-3xl font-bold text-gray-800 mb-2">
            {{ isEditMode ? 'Edit Category' : 'New Category' }}
          </h1>
          <p class="text-gray-600">
            {{ isEditMode ? 'Update your category details' : 'Create a new category for your products' }}
          </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
          <form @submit.prevent="submit">
            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              <!-- English Name -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  English Name *
                </label>
                <input
                  v-model="form.category_name_en"
                  type="text"
                  required
                  placeholder="e.g., Electronics"
                  :class="[
                    'w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition',
                    form.errors.category_name_en ? 'border-red-400' : 'border-gray-300 hover:border-gray-400'
                  ]"
                />
                <p v-if="form.errors.category_name_en" class="text-sm text-red-500">
                  {{ form.errors.category_name_en }}
                </p>
              </div>

              <!-- Bangla Name -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  Bangla Name
                </label>
                <input
                  v-model="form.category_name_bn"
                  type="text"
                  placeholder="e.g., ইলেকট্রনিক্স"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition hover:border-gray-400"
                />
                <p v-if="form.errors.category_name_bn" class="text-sm text-red-500">
                  {{ form.errors.category_name_bn }}
                </p>
              </div>

              <!-- Parent Category -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  Parent Category
                </label>
                <select
                  v-model="form.parent_id"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition hover:border-gray-400"
                >
                  <option :value="null">No Parent (Main Category)</option>
                  <option
                    v-for="parentCat in availableParentCategories"
                    :key="parentCat.id"
                    :value="parentCat.id"
                    :disabled="isEditMode && categoryToEdit?.id === parentCat.id"
                  >
                    {{ parentCat.category_name_en }}
                    <span v-if="parentCat.category_name_bn"> - {{ parentCat.category_name_bn }}</span>
                  </option>
                </select>
              </div>

              <!-- Status -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  Status
                </label>
                <div class="flex space-x-6">
                  <label class="flex items-center cursor-pointer">
                    <input
                      type="radio"
                      v-model="form.status"
                      :value="1"
                      class="h-4 w-4 text-blue-600"
                    />
                    <span class="ml-2 text-gray-700">Active</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      type="radio"
                      v-model="form.status"
                      :value="0"
                      class="h-4 w-4 text-blue-600"
                    />
                    <span class="ml-2 text-gray-700">Inactive</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col sm:flex-row gap-4 justify-between">
              <Link
                :href="route('categories.index')"
                class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-center"
              >
                ← Back to Categories
              </Link>
              
              <button
                type="submit"
                :disabled="form.processing"
                :class="[
                  'px-8 py-3 rounded-lg font-medium transition flex items-center justify-center min-w-[180px]',
                  form.processing
                    ? 'bg-blue-400 cursor-not-allowed'
                    : 'bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg'
                ]"
              >
                <span v-if="form.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ isEditMode ? 'Updating...' : 'Creating...' }}
                </span>
                <span v-else class="text-white">
                  {{ isEditMode ? 'Update Category' : 'Create Category' }}
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  categories: Array
});

const loading = ref(false);

// Get edit ID from URL
const urlParams = new URLSearchParams(window.location.search);
const editId = urlParams.get('id');
const isEditMode = computed(() => !!editId);

// Find category to edit
const categoryToEdit = computed(() => {
  return editId ? props.categories.find(cat => cat.id.toString() === editId) : null;
});

// Filter available parent categories (prevent circular reference)
const availableParentCategories = computed(() => {
  if (!isEditMode.value || !categoryToEdit.value) {
    return props.categories;
  }

  const excludeIds = new Set();
  
  // Recursively collect all descendants
  const collectDescendants = (id) => {
    excludeIds.add(id);
    const children = props.categories.filter(cat => cat.parent_id === id);
    children.forEach(child => collectDescendants(child.id));
  };
  
  collectDescendants(categoryToEdit.value.id);
  
  return props.categories.filter(cat => !excludeIds.has(cat.id));
});

// Form setup
const form = useForm({
  category_name_en: '',
  category_name_bn: '',
  parent_id: null,
  status: 1,
});

// Pre-fill form when editing
watch(() => categoryToEdit.value, (category) => {
  if (category) {
    form.category_name_en = category.category_name_en || '';
    form.category_name_bn = category.category_name_bn || '';
    form.parent_id = category.parent_id || null;
    form.status = Number(category.status) || 1;
  }
}, { immediate: true });

// Submit handler
const submit = () => {
  const routeMethod = isEditMode.value ? 'put' : 'post';
  const routeUrl = isEditMode.value 
    ? route('categories.update', categoryToEdit.value.id)
    : route('categories.store');

  form[routeMethod](routeUrl, {
    preserveScroll: true,
    onSuccess: () => router.visit(route('categories.index'))
  });
};
</script>