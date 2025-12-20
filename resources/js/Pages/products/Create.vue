<template>
  <div class="p-4 max-w-4xl mx-auto">
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
      <p class="mt-2 text-gray-600">Loading...</p>
    </div>

    <div v-else>
      <h1 class="text-2xl font-bold mb-6">
        {{ product ? 'Edit Product' : 'Create Product' }}
      </h1>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Basic Information Section -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold mb-4 pb-2 border-b">Basic Information</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Product Name (EN) *</label>
              <input v-model="form.product_name_en" type="text" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500" />
              <div v-if="form.errors.product_name_en" class="text-red-500 text-sm mt-1">{{ form.errors.product_name_en }}</div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Product Name (BN)</label>
              <input v-model="form.product_name_bn" type="text" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500" />
              <div v-if="form.errors.product_name_bn" class="text-red-500 text-sm mt-1">{{ form.errors.product_name_bn }}</div>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select v-model="form.category_id" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500">
                <option value="0">Select Category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.category_name_en }}</option>
              </select>
              <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">{{ form.errors.category_id }}</div>
            </div>
          </div>
        </div>

        <!-- Pricing Section -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold mb-4 pb-2 border-b">Pricing & Inventory</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
              <input v-model="form.price" type="number" step="0.01" min="0" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500" />
              <div v-if="form.errors.price" class="text-red-500 text-sm mt-1">{{ form.errors.price }}</div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Discount Price</label>
              <input v-model="form.discount_price" type="number" step="0.01" min="0" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
              <input v-model="form.quantity" type="number" min="0" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500" />
              <div v-if="form.errors.quantity" class="text-red-500 text-sm mt-1">{{ form.errors.quantity }}</div>
            </div>

            <div class="mb-4">
              <label class="flex items-center space-x-2">
                <input v-model="form.is_in_stock" type="checkbox" class="rounded text-blue-500" />
                <span class="text-sm font-medium text-gray-700">In Stock</span>
              </label>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select v-model="form.status" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500">
                <option :value="1">Active</option>
                <option :value="0">Inactive</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Media Section -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold mb-4 pb-2 border-b">Media</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Main Image</label>
              <input type="file" @change="handleImageUpload" accept="image/*" class="border border-gray-300 rounded p-2 w-full" />
              
              <!-- Show existing image if in edit mode -->
              <div v-if="product && product.image_url" class="mt-2">
                <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                <img :src="product.image_url" alt="Current product image" class="h-20 w-20 object-cover rounded border" />
              </div>
              
              <div v-if="imagePreview" class="mt-2">
                <span class="text-sm text-gray-600">New image selected: {{ imagePreview }}</span>
              </div>
              <div v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}</div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Multiple Images</label>
              <input type="file" multiple @change="handleMultipleImages" accept="image/*" class="border border-gray-300 rounded p-2 w-full" />
              
              <!-- Show existing images if in edit mode -->
              <div v-if="product && product.images && product.images.length > 0" class="mt-2">
                <p class="text-sm text-gray-600 mb-2">Current Images ({{ product.images.length }}):</p>
                <div class="flex space-x-2">
                  <img v-for="(img, index) in product.images" :key="index" :src="img.url" alt="Gallery image" class="h-16 w-16 object-cover rounded border" />
                </div>
              </div>
              
              <div v-if="imagesCount > 0" class="mt-2">
                <span class="text-sm text-gray-600">{{ imagesCount }} new images selected</span>
              </div>
              <div v-if="form.errors.images" class="text-red-500 text-sm mt-1">{{ form.errors.images }}</div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Video</label>
              <input type="file" @change="handleVideoUpload" accept="video/*" class="border border-gray-300 rounded p-2 w-full" />
              
              <!-- Show existing video if in edit mode -->
              <div v-if="product && product.video_url" class="mt-2">
                <p class="text-sm text-gray-600 mb-2">Current Video:</p>
                <video :src="product.video_url" class="h-20 w-20 object-cover rounded border" controls></video>
              </div>
              
              <div v-if="videoPreview" class="mt-2">
                <span class="text-sm text-gray-600">New video selected: {{ videoPreview }}</span>
              </div>
              <div v-if="form.errors.video" class="text-red-500 text-sm mt-1">{{ form.errors.video }}</div>
            </div>
          </div>
        </div>

        <!-- Description Section -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold mb-4 pb-2 border-b">Descriptions</h2>
          <div class="space-y-4">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Short Description (EN)</label>
              <textarea v-model="form.short_description_en" rows="2" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500"></textarea>
              <div v-if="form.errors.short_description_en" class="text-red-500 text-sm mt-1">{{ form.errors.short_description_en }}</div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Short Description (BN)</label>
              <textarea v-model="form.short_description_bn" rows="2" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500"></textarea>
              <div v-if="form.errors.short_description_bn" class="text-red-500 text-sm mt-1">{{ form.errors.short_description_bn }}</div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Description (EN)</label>
              <textarea v-model="form.description_en" rows="4" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500"></textarea>
              <div v-if="form.errors.description_en" class="text-red-500 text-sm mt-1">{{ form.errors.description_en }}</div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Description (BN)</label>
              <textarea v-model="form.description_bn" rows="4" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500"></textarea>
              <div v-if="form.errors.description_bn" class="text-red-500 text-sm mt-1">{{ form.errors.description_bn }}</div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Calculation Details</label>
              <textarea v-model="form.calculation" rows="3" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
          </div>
        </div>

        <!-- Seller Information Section -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold mb-4 pb-2 border-b">Seller Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Seller Details</label>
              <textarea v-model="form.seller_details" rows="3" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
              <input v-model="form.mobile_no" type="tel" class="border border-gray-300 rounded p-2 w-full focus:ring-blue-500 focus:border-blue-500" />
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button type="button" @click="cancel" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition duration-150">
            Cancel
          </button>
          <button type="submit" :disabled="form.processing || loading" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150">
            <span v-if="form.processing">Saving...</span>
            <span v-else>{{ product ? 'Update Product' : 'Save Product' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  categories: { type: Array, default: () => [] },
});

const loading = ref(false);
const product = ref(null);
const productId = ref(null);

// Check if we're in edit mode by looking for ID in URL
const isEditMode = computed(() => productId.value !== null);

// Initialize form
const form = useForm({
  product_name_en: '',
  product_name_bn: '',
  category_id: 0,
  price: '',
  discount_price: '',
  quantity: 0,
  is_in_stock: true,
  status: 1,
  image: null,
  images: [],
  video: null,
  description_en: '',
  description_bn: '',
  short_description_en: '',
  short_description_bn: '',
  calculation: '',
  seller_details: '',
  mobile_no: '',
});

// Previews
const imagePreview = ref('');
const imagesCount = ref(0);
const videoPreview = ref('');

// Check URL for product ID on component mount
onMounted(async () => {
  const url = new URL(window.location.href);
  const id = url.searchParams.get('id');
  
  if (id) {
    productId.value = id;
    await fetchProduct(id);
  }
});

// Fetch product data from API
const fetchProduct = async (id) => {
  try {
    loading.value = true;
    const response = await axios.get(route('api.products.show', id));
    product.value = response.data;
    
    // Populate form with existing data
    Object.keys(form).forEach(key => {
      if (key in product.value) {
        form[key] = product.value[key];
      }
    });
    
    // Convert status to number if it's coming as string
    form.status = parseInt(product.value.status);
    form.category_id = parseInt(product.value.category_id);
    form.is_in_stock = Boolean(product.value.is_in_stock);
    
  } catch (error) {
    console.error('Error fetching product:', error);
    // Optionally show an error message to user
  } finally {
    loading.value = false;
  }
};

// File upload handlers
const handleImageUpload = (e) => { 
  const file = e.target.files[0]; 
  if(file){ 
    form.image = file; 
    imagePreview.value = file.name; 
  } 
}

const handleMultipleImages = (e) => { 
  const files = Array.from(e.target.files); 
  form.images = files; 
  imagesCount.value = files.length; 
}

const handleVideoUpload = (e) => { 
  const file = e.target.files[0]; 
  if(file){ 
    form.video = file; 
    videoPreview.value = file.name; 
  } 
}

// Submit form
const submit = () => {
  const method = isEditMode.value ? 'put' : 'post';
  const routeName = isEditMode.value ? route('products.update', productId.value) : route('products.store');

  // Convert boolean to integer
  const submissionData = { 
    ...form.data(), 
    is_in_stock: form.is_in_stock ? 1 : 0, 
    status: parseInt(form.status),
    _method: method === 'put' ? 'PUT' : 'POST' // For Laravel method spoofing
  };

  form.submit(method, routeName, {
    forceFormData: true,
    preserveScroll: true,
    data: submissionData,
    onSuccess: () => {
      console.log('Success');
      router.visit(route('products.index'));
    },
    onError: (e) => {
      console.log('Errors', e);
    },
  });
}

const cancel = () => { 
  router.visit(route('products.index')); 
}
</script>

<style scoped>
/* Optional: Add custom styles if needed */
</style>