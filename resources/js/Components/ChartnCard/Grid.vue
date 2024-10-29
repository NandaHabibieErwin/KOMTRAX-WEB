<script setup>
import Card from '@/Components/ChartnCard/Card.vue';

</script>


    <template>

        <v-row justify="center" align="start" class="gap-1">
            <v-col v-for="(chart, index) in charts" :key="index" cols="12"
            sm="6"
            md="4"
            lg="3">
                <Card :chart="chart" :SelectedSeries="SelectedSeries" />

             <!--   <apexchart :options="chart.options" :series="chart.series" width="350px" height="253px" type="area">
                </apexchart>-->
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
      SelectedSeries: String,
    },
  };
  </script>
