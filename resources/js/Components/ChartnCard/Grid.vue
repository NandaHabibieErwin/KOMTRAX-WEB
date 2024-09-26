<script setup>
import LineChart from '@/Components/ChartnCard/LineChart.vue';
import Card from '@/Components/ChartnCard/Card.vue';

const limit = 1;

</script>
<template>

      <v-row no-gutters>
        <v-col v-for="n in limit" key="n">
          <LineChart class="bg-white"></LineChart>
        </v-col>
        <v-responsive width="100%"></v-responsive>
      </v-row>

  </template>
