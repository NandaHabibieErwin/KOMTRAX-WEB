<template>
    <div class="pa-4 text-center">
        <v-dialog v-model="dialog" max-width="600">
            <template v-slot:activator="{ props: activatorProps }">
                <v-fab color="yellow" icon="mdi-book-plus" variant="elevated" v-bind="activatorProps"
                    class="position-fixed bottom-0 right-0 mb-14 mr-20">
                </v-fab>
            </template>

            <v-card prepend-icon="mdi-book-plus" title="Add Enroll">
                <v-card-text>
                    <v-row dense>
                        <v-col cols="12" md="4" sm="6">
                            <v-text-field v-model="filter.nama_filter" label="Nama Filter*" required></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="12" sm="12">
                            <v-textarea hint="1,2,3" v-model="filter.machine" label="Mesin"></v-textarea>
                        </v-col>
                    </v-row>

                    <small class="text-caption text-medium-emphasis">*indicates required field</small>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn text="Close" variant="plain" @click="dialog = false"></v-btn>

                    <v-btn color="primary" text="Save" variant="tonal" @click="SaveFilter"></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
import axios from 'axios';
import { Head, usePage } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
export default {

    props: {
        UpdateDisplay: Function,
        handleTabChange: Function,
        user: Object,
        filters: Array,
    },

    setup(props) {
        const dialog = ref(false);
        const filter = reactive({
            nama_filter: '',
            machine: '',
        });

        const SaveFilter = async () => {
            try {
                const response = await axios.post('/save-filter', filter);
                dialog.value = false;
                props.UpdateDisplay(response.data.enroll);
                const NewFilter = response.data.enroll.nama_filter;
                const NewIndex = props.filters.findIndex(f => f.nama_filter === NewFilter);
                console.log(NewIndex);
                props.handleTabChange(NewIndex);
            } catch (error) {
                console.error('Failed:', error);
            }
        };

        return {
            dialog,
            filter,
            SaveFilter,
        };
    },
};
</script>
