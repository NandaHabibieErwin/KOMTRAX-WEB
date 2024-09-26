<script setup>
import LineChart from '@/Components/ChartnCard/LineChart.vue';
</script>

<template>
    <v-card
      class="mx-auto"
      prepend-icon="$vuetify"
      subtitle="The #1 Vue UI Library"
      width="400"
    >
      <template v-slot:title>
        <span class="font-weight-black">Welcome to Vuetify</span>
      </template>

      <v-card-text class="bg-surface-light pt-4">
        <LineChart class="bg-white"></LineChart>
      </v-card-text>
    </v-card>
  </template>
