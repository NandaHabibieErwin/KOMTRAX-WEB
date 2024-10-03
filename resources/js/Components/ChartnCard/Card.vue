<script setup>
import LineChart from '@/Components/ChartnCard/LineChart.vue';
import { defineProps } from 'vue';


const props = defineProps({
  chart: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <v-card class="mx-auto w-96">
    <v-card-item>
        <v-card-title>
            {{ chart.ChartTitle }}
          </v-card-title>
          <v-card-subtitle v-html="chart.AdditionalTitle">
          </v-card-subtitle>
      </v-card-item>
    <v-card-text class="bg-white">
      <apexchart :options="chart.options" :series="chart.series" width="350px" height="253px" type="area" />
    </v-card-text>
  </v-card>
</template>
