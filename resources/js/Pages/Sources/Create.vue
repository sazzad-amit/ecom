<template>
  <AppLayout>
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
          {{ form.id ? 'Edit Source' : 'Create Source' }}
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
            <label for="source_name_en" class="block text-sm font-medium text-gray-700 mb-1">
              Source Name (English) *
            </label>
            <input
              v-model="form.source_name_en"
              id="source_name_en"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
              :class="{ 'border-red-500': errors.source_name_en }"
            />
            <p v-if="errors.source_name_en" class="mt-1 text-sm text-red-600">{{ errors.source_name_en }}</p>
          </div>

          <!-- Bangla Name -->
          <div>
            <label for="source_name_bn" class="block text-sm font-medium text-gray-700 mb-1">
              Source Name (Bangla)
            </label>
            <input
              v-model="form.source_name_bn"
              id="source_name_bn"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
            />
          </div>

          <!-- Auto ID -->
          <div>
            <label for="source_auto_id" class="block text-sm font-medium text-gray-700 mb-1">
              Auto ID *
            </label>
            <input
              v-model="form.source_auto_id"
              id="source_auto_id"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors"
              :class="{ 'border-red-500': errors.source_auto_id }"
            />
            <p v-if="errors.source_auto_id" class="mt-1 text-sm text-red-600">{{ errors.source_auto_id }}</p>
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

          <!-- English Details CKEditor -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Details (English)
            </label>
            <ckeditor :editor="editor" v-model="form.details_en" :config="editorConfig" />
          </div>

          <!-- Bangla Details CKEditor -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Details (Bangla)
            </label>
            <ckeditor :editor="editor" v-model="form.details_bn" :config="editorConfig" />
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
import AppLayout from '@/Layouts/AppLayout.vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
import { Ckeditor } from '@ckeditor/ckeditor5-vue'

export default {
  components: { 
    AppLayout,
    ckeditor: Ckeditor  // Register with lowercase name
  },

  props: {
    source: {
      type: Object,
      default: null
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },

  data() {
    return {
      editor: ClassicEditor,
      editorConfig: {
        toolbar: [
          'heading', '|',
          'bold', 'italic', '|',
          'link', 'bulletedList', 'numberedList', '|',
          'blockQuote', 'insertTable', '|',
          'undo', 'redo'
        ],
      },
      form: {
        source_name_en: '',
        source_name_bn: '',
        source_auto_id: '',
        mobile_no: '',
        details_en: '',
        details_bn: '',
        status: true,
      },
      processing: false,
    }
  },

  mounted() {
    if (this.source) {
      this.form = {
        id: this.source.id,
        source_name_en: this.source.source_name_en || '',
        source_name_bn: this.source.source_name_bn || '',
        source_auto_id: this.source.source_auto_id || '',
        mobile_no: this.source.mobile_no || '',
        details_en: this.source.details_en || '',
        details_bn: this.source.details_bn || '',
        status: Boolean(this.source.status),
      }
    }
  },

  methods: {
    submitForm() {
      this.processing = true
      const request = this.form.id
        ? this.$inertia.put(`/sources/${this.form.id}`, this.form)
        : this.$inertia.post('/sources', this.form)
      
      request.finally(() => {
        this.processing = false
      })
    },

    goBack() {
      this.$inertia.get('/sources')
    },
  },
}
</script>