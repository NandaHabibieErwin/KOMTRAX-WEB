<template>
    <v-container fluid>
        <v-row class="pb-5" justify="center">
            <v-col cols="12">
                <v-card v-if="hasAverages" class="w-screen">
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
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
    </v-container>
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
        }
    },
    computed: {
        hasAverages() {
            return (
                this.averageFuelConsumption !== 0 ||
                this.averageIdlingRatio !== 0 ||
                this.averagePModeRatio !== 0
            );
        },

        cardTitle() {
            let title = 'Machine Averages';

            if (this.averageFuelConsumption !== 0) {
                title = 'Average Fuel Consumption';
            }
            if (this.averageIdlingRatio !== 0) {
                title = 'Average Idling Ratio';
            }
            if (this.averagePModeRatio !== 0) {
                title = 'Average P Mode Ratio';
            }

            return title;
        }
    }
};
</script>
