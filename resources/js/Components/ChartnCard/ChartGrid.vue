<template>
    <EnrollCard v-if="!Loading" :averageFuelConsumption="averageFuelConsumption"
        :averageIdlingRatio="averageIdlingRatio" :averagePModeRatio="averagePModeRatio" />
</template>

<script>
export default {
    props: ['Loading', 'averageFuelConsumption', 'averageIdlingRatio', 'averagePModeRatio'],
};
</script>
