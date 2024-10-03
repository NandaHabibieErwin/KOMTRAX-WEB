<template>
    <v-card>
      <v-tabs
        v-model="tab"
        align-tabs="center"
        bg-color="deep-purple-accent-4"
        stacked
      >
        <v-tab value="tab-1">
          <v-icon icon="mdi-phone"></v-icon>

          Working Hour
        </v-tab>

        <v-tab value="tab-2">
          <v-icon icon="mdi-heart"></v-icon>

          Idle Ratio
        </v-tab>

        <v-tab value="tab-3">
          <v-icon icon="mdi-account-box"></v-icon>

          Fuel Consumption
        </v-tab>
      </v-tabs>

      <v-tabs-window v-model="tab">
        <v-tabs-window-item
          v-for="i in 3"
          :key="i"
          :value="'tab-' + i"
        >
          <v-card>
            <v-card-text>{{ text }}</v-card-text>
          </v-card>
        </v-tabs-window-item>
      </v-tabs-window>
    </v-card>
  </template>

  <script>
  export default {
    data () {
      return {
        tab: null,
        text: '',
      }
    },
  }
</script>
