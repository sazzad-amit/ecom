<template>
  <AppLayout>
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
          {{ form.id ? 'Edit Receiver' : 'Create Receiver' }}
        </h1>
        <button
          @click="goBack"
          class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
        >
          Back to List
        </button>
      </div>

      <form @submit.prevent="submitForm" class="bg-white shadow-sm rounded-lg p-6 border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- English Name -->
          <div>
            <label for="receiver_name_en" class="block text-sm font-medium text-gray-700 mb-1">
              Receiver Name (English) *
            </label>
            <input
              v-model="form.receiver_name_en"
              id="receiver_name_en"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
              :class="{ 'border-red-500': errors.receiver_name_en }"
            />
            <p v-if="errors.receiver_name_en" class="mt-1 text-sm text-red-600">{{ errors.receiver_name_en }}</p>
          </div>

          <!-- Bangla Name -->
          <div>
            <label for="receiver_name_bn" class="block text-sm font-medium text-gray-700 mb-1">
              Receiver Name (Bangla)
            </label>
            <input
              v-model="form.receiver_name_bn"
              id="receiver_name_bn"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
            />
          </div>

          <!-- Auto ID -->
          <div>
            <label for="receiver_auto_id" class="block text-sm font-medium text-gray-700 mb-1">
              Auto ID *
            </label>
            <input
              v-model="form.receiver_auto_id"
              id="receiver_auto_id"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
              :class="{ 'border-red-500': errors.receiver_auto_id }"
            />
            <p v-if="errors.receiver_auto_id" class="mt-1 text-sm text-red-600">{{ errors.receiver_auto_id }}</p>
          </div>

          <!-- Mobile Number -->
          <div>
            <label for="mobile_no" class="block text-sm font-medium text-gray-700 mb-1">
              Mobile Number
            </label>
            <input
              v-model="form.mobile_no"
              id="mobile_no"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
            />
          </div>

          <!-- English Details -->
          <div class="md:col-span-2">
            <label for="details_en" class="block text-sm font-medium text-gray-700 mb-1">
              Details (English)
            </label>
            <textarea
              v-model="form.details_en"
              id="details_en"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
            ></textarea>
          </div>

          <!-- Bangla Details -->
          <div class="md:col-span-2">
            <label for="details_bn" class="block text-sm font-medium text-gray-700 mb-1">
              Details (Bangla)
            </label>
            <textarea
              v-model="form.details_bn"
              id="details_bn"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
            ></textarea>
          </div>

          <!-- Status -->
          <div>
            <label class="flex items-center">
              <input
                v-model="form.status"
                type="checkbox"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              />
              <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-end">
          <button
            type="submit"
            :disabled="processing"
            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ processing ? 'Processing...' : (form.id ? 'Update' : 'Create') }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  components: { AppLayout },
  props: {
    receiver: Object,
    errors: Object,
  },
  data() {
    return {
      form: {
        receiver_name_en: '',
        receiver_name_bn: '',
        receiver_auto_id: '',
        mobile_no: '',
        details_en: '',
        details_bn: '',
        status: true,
      },
      processing: false,
    }
  },
  mounted() {
    if (this.receiver) {
      this.form = {
        ...this.receiver,
        status: Boolean(this.receiver.status),
      };
    }
  },
  methods: {
    submitForm() {
      this.processing = true;
      
      if (this.form.id) {
        this.$inertia.put(`/receivers/${this.form.id}`, this.form, {
          onSuccess: () => {
            this.processing = false;
          },
          onError: () => {
            this.processing = false;
          }
        });
      } else {
        this.$inertia.post('/receivers', this.form, {
          onSuccess: () => {
            this.processing = false;
          },
          onError: () => {
            this.processing = false;
          }
        });
      }
    },
    goBack() {
      this.$inertia.get('/receivers');
    }
  }
}
</script>