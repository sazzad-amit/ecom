<template>
  <div class="relative">
    <!-- Vertical Branch (THICK) -->
    <div
      v-if="level > 0"
      class="absolute top-0 bottom-0 rounded-full"
      :class="lineColor"
      :style="{
        left: (level * 26 - 13) + 'px',
        width: '4px'
      }"
    ></div>

    <div
      :style="{ paddingLeft: (level * 26) + 'px' }"
      class="relative flex items-center py-2"
    >
      <!-- Horizontal Branch (THICK) -->
      <div
        v-if="level > 0"
        class="absolute rounded-full"
        :class="lineColor"
        :style="{
          left: (level * 26 - 13) + 'px',
          width: '18px',
          height: '4px'
        }"
      ></div>

      <!-- Expand / Collapse Button -->
      <button
        v-if="hasChildren"
        @click.stop="toggle"
        class="relative z-10 w-7 h-7 mr-2
               rounded-lg font-extrabold shadow-md
               flex items-center justify-center
               transition hover:scale-110"
        :class="iconStyle"
      >
        {{ isExpanded ? '−' : '+' }}
      </button>

      <span v-else class="w-9"></span>

      <!-- Category Label (LODGE STYLE) -->
      <div
        @click.stop="selectNode"
        class="relative z-10 px-5 py-2
               rounded-2xl font-extrabold tracking-wide
               shadow-lg cursor-pointer
               transition hover:scale-[1.03]"
        :class="labelStyle"
      >
        {{ node.category_name_en }}
      </div>
    </div>

    <!-- Children -->
    <div v-if="hasChildren && isExpanded" class="mt-1">
      <TreeNode
        v-for="child in node.children"
        :key="child.id"
        :node="child"
        :level="level + 1"
        @select="handleChildSelect"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  node: Object,
  level: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['select'])
const isExpanded = ref(true)

const hasChildren = computed(() =>
  props.node.children && props.node.children.length > 0
)

/**
 * 🌈 VIBGYOR palette (1–7 repeat)
 */
const colors = [
  { text: 'text-violet-800', line: 'bg-violet-700', bg: 'bg-violet-100', icon: 'bg-violet-600 text-white' },
  { text: 'text-indigo-800', line: 'bg-indigo-700', bg: 'bg-indigo-100', icon: 'bg-indigo-600 text-white' },
  { text: 'text-blue-800',   line: 'bg-blue-700',   bg: 'bg-blue-100',   icon: 'bg-blue-600 text-white' },
  { text: 'text-green-800',  line: 'bg-green-700',  bg: 'bg-green-100',  icon: 'bg-green-600 text-white' },
  { text: 'text-yellow-800', line: 'bg-yellow-600', bg: 'bg-yellow-100', icon: 'bg-yellow-500 text-black' },
  { text: 'text-orange-800', line: 'bg-orange-700', bg: 'bg-orange-100', icon: 'bg-orange-600 text-white' },
  { text: 'text-red-800',    line: 'bg-red-700',    bg: 'bg-red-100',    icon: 'bg-red-600 text-white' }
]

const colorIndex = computed(() => props.level % 7)
const palette = computed(() => colors[colorIndex.value])

const lineColor = computed(() => palette.value.line)
const iconStyle = computed(() => palette.value.icon)
const labelStyle = computed(() => `${palette.value.bg} ${palette.value.text}`)

const toggle = () => {
  isExpanded.value = !isExpanded.value
}

const selectNode = () => {
  emit('select', {
    ...props.node,
    category_id: parseInt(props.node.id)  // Simple category_id only
  })
}

const handleChildSelect = (childNode) => {
  emit('select', childNode)  // Just pass through the child node
}
</script>