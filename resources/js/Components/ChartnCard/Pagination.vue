<template>
    <v-row v-if="!Loading">
        <v-col cols="12">
            <v-pagination class="pagination mb-2" v-model="page" :length="TotalPages" total-visible="7"
                @input="UpdatePaginatedCharts"></v-pagination>
        </v-col>
    </v-row>
</template>

<script>
export default {
    props: ['page', 'TotalPages', 'Loading', 'UpdatePaginatedCharts'],
    data() {
        return {};
    }
};
</script>
