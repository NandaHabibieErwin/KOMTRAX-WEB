<script setup>
import LineChart from '@/Components/ChartnCard/LineChart.vue';
import {computed, defineProps } from 'vue';



const props = defineProps({
  chart: {
    type: Object,
    required: true,
  },
  SelectedSeries: String,
});


const ChartType = computed(() =>  {

      if (props.SelectedSeries === 'Working Hour') {
        return 'area';
      } else if (props.SelectedSeries === 'Fuel Consumption') {
        return 'area';
      } else if (props.SelectedSeries === 'Idling Hour') {
        return 'area';
      } else if (props.SelectedSeries === 'EMode') {
        return 'bar';
      } else if (props.SelectedSeries === 'PMode') {
        return 'bar';
      } else {
        return 'area';
      }
    }
);



</script>

<template>
  <v-card class="mr-2 w-96">
    <v-card-item>
        <v-card-title>
            {{ chart.ChartTitle }}
          </v-card-title>
          <v-card-subtitle v-html="chart.AdditionalTitle">
          </v-card-subtitle>
      </v-card-item>
    <v-card-text class="bg-white">
      <apexchart :options="chart.options" :series="chart.series" width="350px" height="253px" />
    </v-card-text>
  </v-card>
</template>
