<template>
    <v-row class="pb-5 mb-2 px-3">
        <v-col cols="12">
            <!-- Single Card for Dynamic Title and Content -->
            <v-card v-if="hasAverages">
                <v-card-title>{{ cardTitle }}</v-card-title>
                <v-card-text>
                    <div v-if="averageFuelConsumption !== null && averageFuelConsumption !== 0">
                        <strong>Fuel Consumption:</strong> {{ averageFuelConsumption }} L/H
                    </div>
                    <div v-if="averageIdlingRatio !== null && averageIdlingRatio !== 0">
                        <strong>Idling Ratio:</strong> {{ averageIdlingRatio }} %
                    </div>
                    <div v-if="averagePModeRatio !== null && averagePModeRatio !== 0">
                        <strong>P Mode Ratio:</strong> {{ averagePModeRatio }} %
                    </div>
                    <div v-if="averageEModeRatio !== null && averageEModeRatio !== 0">
                        <strong>E Mode Ratio:</strong> {{ averageEModeRatio }} %
                    </div>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
export default {
    props: {
        averageFuelConsumption: {
            type: Number,
            default: 0
        },
        averageIdlingRatio: {
            type: Number,
            default: 0
        },
        averagePModeRatio: {
            type: Number,
            default: 0
        },
        averageEModeRatio: {
            type: Number,
            default: 0
        }
    },
    computed: {
        // Check if any average value is present
        hasAverages() {
            return (
                this.averageFuelConsumption !== 0 ||
                this.averageIdlingRatio !== 0 ||
                this.averagePModeRatio !== 0 ||
                this.averageEModeRatio !== 0
            );
        },
        // Dynamically set the card title based on the available averages
        cardTitle() {
            let title = 'Machine Averages'; // Default title

            if (this.averageFuelConsumption !== 0) {
                title = 'Average Fuel Consumption';
            }
            if (this.averageIdlingRatio !== 0) {
                title = 'Average Idling Ratio';
            }
            if (this.averagePModeRatio !== 0) {
                title = 'Average P Mode Ratio';
            }
            if (this.averageEModeRatio !== 0) {
                title = 'Average E Mode Ratio';
            }

            return title;
        }
    }
};
</script>
