<template>
  <div class="col-md-6">
    <div :class="`card shadow border-0 border-top border-4 border-${color}`">
      <div :class="`card-header bg-${color} text-white d-flex align-items-center`">
        <i :class="`fas ${icon} fa-2x me-3`" aria-hidden="true"></i>
        <h5 class="mb-0 fw-bold">{{ title }}</h5>
      </div>

      <div class="card-body">
        <div v-if="!items || items.length === 0" class="text-muted fst-italic">
          Nenhuma informação disponível.
        </div>
        <div v-else>
          <div v-for="(item, index) in items" :key="index" class="mb-2">
            <template v-if="item.label">
              <strong>{{ item.label }}:</strong> {{ item.value }}
            </template>
            <template v-else>
              <span>• {{ item.value }}</span>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  icon: String,
  title: String,
  items: {
    type: Array,
    default: () => []
  },
  color: {
    type: String,
    default: 'primary'
  }
})
</script>
