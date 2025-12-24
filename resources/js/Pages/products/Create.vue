<template>
  <AppLayout>
    <div class="p-6">
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
        <p class="mt-2 text-gray-600">Loading...</p>
      </div>
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-gray-900">
            {{ isEditMode ? 'Edit Product' : 'Create Product' }}
          </h1>
          <button
            @click="goBack"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
          >
            Back to List
          </button>
        </div>

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

        <!-- Pricing & Inventory -->
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

            <div class="mb-4 flex items-center space-x-2">
              <input v-model="form.is_in_stock" type="checkbox" class="rounded text-blue-500" />
              <span class="text-sm font-medium text-gray-700">In Stock</span>
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
            <!-- Main Image -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Main Image</label>
              <input type="file" @change="handleImageUpload" accept="image/*" class="border border-gray-300 rounded p-2 w-full" />
              <div v-if="isEditMode && product?.image_url" class="mt-2">
                <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                <img :src="product.image_url" alt="Current product image" class="h-20 w-20 object-cover rounded border" />
              </div>
              <div v-if="imagePreview" class="mt-2 text-sm text-gray-600">New image selected: {{ imagePreview }}</div>
              <div v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}</div>
            </div>

            <!-- Multiple Images -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Multiple Images</label>
              <input type="file" multiple @change="handleMultipleImages" accept="image/*" class="border border-gray-300 rounded p-2 w-full" />
              <div v-if="isEditMode && product?.images?.length" class="mt-2">
                <p class="text-sm text-gray-600 mb-2">Current Images ({{ product.images.length }}):</p>
                <div class="flex space-x-2">
                  <img v-for="(img, index) in product.images" :key="index" :src="img.url" alt="Gallery image" class="h-16 w-16 object-cover rounded border" />
                </div>
              </div>
              <div v-if="imagesCount" class="mt-2 text-sm text-gray-600">{{ imagesCount }} new images selected</div>
              <div v-if="form.errors.images" class="text-red-500 text-sm mt-1">{{ form.errors.images }}</div>
            </div>

            <!-- Video -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Video</label>
              <input type="file" @change="handleVideoUpload" accept="video/*" class="border border-gray-300 rounded p-2 w-full" />
              <div v-if="isEditMode && product?.video_url" class="mt-2">
                <p class="text-sm text-gray-600 mb-2">Current Video:</p>
                <video :src="product.video_url" class="h-20 w-20 object-cover rounded border" controls></video>
              </div>
              <div v-if="videoPreview" class="mt-2 text-sm text-gray-600">New video selected: {{ videoPreview }}</div>
              <div v-if="form.errors.video" class="text-red-500 text-sm mt-1">{{ form.errors.video }}</div>
            </div>
          </div>
        </div>

        <!-- Descriptions Section -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold mb-4 pb-2 border-b">Descriptions</h2>
          <div class="space-y-4">
            <textarea v-model="form.short_description_en" rows="2" placeholder="Short Description EN" class="border rounded p-2 w-full"></textarea>
            <textarea v-model="form.short_description_bn" rows="2" placeholder="Short Description BN" class="border rounded p-2 w-full"></textarea>
            <textarea v-model="form.description_en" rows="4" placeholder="Full Description EN" class="border rounded p-2 w-full"></textarea>
            <textarea v-model="form.description_bn" rows="4" placeholder="Full Description BN" class="border rounded p-2 w-full"></textarea>
            <textarea v-model="form.calculation" rows="3" placeholder="Calculation Details" class="border rounded p-2 w-full"></textarea>
          </div>
        </div>

        <!-- Seller Section -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold mb-4 pb-2 border-b">Seller Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <textarea v-model="form.seller_details" rows="3" placeholder="Seller Details" class="border rounded p-2 w-full"></textarea>
            <input v-model="form.mobile_no" type="tel" placeholder="Mobile Number" class="border rounded p-2 w-full" />
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button type="button" @click="cancel" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Cancel</button>
          <button type="submit" :disabled="form.processing || loading" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50">
            {{ form.processing ? 'Saving...' : (isEditMode ? 'Update Product' : 'Save Product') }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  categories: { type: Array, default: () => [] },
});

const loading = ref(false);
const product = ref(null);
const productId = ref(null);

const isEditMode = computed(() => productId.value !== null);

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

const imagePreview = ref('');
const imagesCount = ref(0);
const videoPreview = ref('');

onMounted(async () => {
  const url = new URL(window.location.href);
  const id = url.searchParams.get('id');
  if (id) {
    productId.value = id;
    await fetchProduct(id);
  }
});

const fetchProduct = async (id) => {
  try {
    loading.value = true;
    const { data } = await axios.get(route('api.products.show', id));
    product.value = data;

    Object.keys(form).forEach(key => {
      if (key in product.value && product.value[key] !== null) form[key] = product.value[key];
    });

    form.status = parseInt(product.value.status);
    form.category_id = parseInt(product.value.category_id || 0);
    form.is_in_stock = Boolean(product.value.is_in_stock);

  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const handleImageUpload = e => {
  const file = e.target.files[0];
  if (file) {
    form.image = file;
    imagePreview.value = file.name;
  }
};

const handleMultipleImages = e => {
  const files = Array.from(e.target.files);
  form.images = files;
  imagesCount.value = files.length;
};

const handleVideoUpload = e => {
  const file = e.target.files[0];
  if (file) {
    form.video = file;
    videoPreview.value = file.name;
  }
};

const submit = () => {
  const method = isEditMode.value ? 'put' : 'post';
  const routeName = isEditMode.value ? route('products.update', productId.value) : route('products.store');

  form.submit(method, routeName, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => router.visit(route('products.index')),
    onError: e => console.log('Errors', e),
  });
};

const cancel = () => router.visit(route('products.index'));
</script>
