<template>
  <div class="relative">
    <!-- Vertical connector line -->
    <div
      v-if="level > 0"
      class="absolute left-0 top-0 bottom-0 border-l border-gray-400"
      :style="{ left: (level * 16 - 8) + 'px' }"
    ></div>

    <div
      :style="{ paddingLeft: (level * 16) + 'px' }"
      class="relative flex items-center py-1 pl-2 hover:bg-gray-100 rounded cursor-pointer group"
    >
      <!-- Horizontal connector -->
      <span
        v-if="level > 0"
        class="absolute border-t border-gray-400 w-4"
        :style="{ left: (level * 16 - 8) + 'px' }"
      ></span>

      <!-- Toggle icon -->
      <span
        v-if="hasChildren"
        @click.stop="toggle"
        class="w-4 mr-1 text-gray-600 text-xs select-none"
      >
        {{ isExpanded ? '▼' : '▶' }}
      </span>
      <span v-else class="w-4 mr-1"></span>

      <!-- Node label -->
      <span
        @click.stop="selectNode"
        class="text-blue-700 group-hover:underline"
      >
        {{ node.category_name_en }}
      </span>
    </div>

    <!-- Children -->
    <div v-if="hasChildren && isExpanded">
      <TreeNode
        v-for="child in node.children"
        :key="child.id"
        :node="child"
        :level="level + 1"
        @select="$emit('select', $event)"
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

const toggle = () => {
  isExpanded.value = !isExpanded.value
}

const selectNode = () => {
  emit('select', props.node)
}
</script>
