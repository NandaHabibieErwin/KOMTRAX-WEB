<template>
    <v-sheet elevation="6">
        <v-tabs v-model="SelectedSeriesTab">
            <v-tab @click="handleSeriesTabClick(0)">Working Hours</v-tab>
            <v-tab @click="handleSeriesTabClick(1)">Fuel Consumption</v-tab>
            <v-tab @click="handleSeriesTabClick(2)">Idling Ratio</v-tab>
            <v-tab @click="handleSeriesTabClick(3)">E Mode</v-tab>
            <v-tab @click="handleSeriesTabClick(4)">P Mode</v-tab>
        </v-tabs>
    </v-sheet>
</template>

<script>
export default {
    props: ['SelectedSeriesTab'],
    methods: {
        handleSeriesTabClick(newTab) {
            this.$emit('updateSeries', newTab);
        }
    }
};
</script>
