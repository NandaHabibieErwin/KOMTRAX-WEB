<script setup>
import LineChart from '@/Components/ChartnCard/LineChart.vue';
import Card from '@/Components/ChartnCard/Card.vue';

</script>


    <template>
        <v-row no-gutters>
            <v-col v-for="(chart, index) in charts" :key="index">
                <h3>Sheet {{ index + 1 }}</h3>
                <apexchart :options="chart.options" :series="chart.series" width="350px" height="253px" type="area">
                </apexchart>
            </v-col>
            <v-responsive width="100%"></v-responsive>
        </v-row>
    </template>

<script>
  export default {
    name: 'Grid',
    props: {
      charts: {
        type: Array,
        required: true,
      },
    },
  };
  </script>
