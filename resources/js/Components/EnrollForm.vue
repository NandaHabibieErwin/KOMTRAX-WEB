<template>
    <div class="pa-4 text-center">
        <v-dialog v-model="dialog" max-width="600">
            <template v-slot:activator="{ props: activatorProps }">
                <v-btn class="text-none font-weight-regular" prepend-icon="mdi-account" text="Edit Profile"
                    variant="tonal" v-bind="activatorProps"></v-btn>
            </template>

            <v-card prepend-icon="mdi-account" title="User Profile">
                <v-card-text>
                    <v-row dense>
                        <v-col cols="12" md="4" sm="6">
                            <v-text-field v-model="filter.nama_filter" label="Nama Filter*" required></v-text-field>
                        </v-col>

                        <v-col cols="12" md="4" sm="6">
                            <v-text-field hint="1,2,3" v-model="filter.machine" label="Mesin"></v-text-field>
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

    setup() {
        const { props } = usePage();
        const dialog = ref(false);
        const filter = reactive({
            nama_filter: '',
            machine: '',
            idUser: props.user.id,
        });

        const SaveFilter = async () => {
            try {
                await axios.post('/save-filter', filter);
                dialog.value = false;
            } catch (error) {
                console.error('Failed:', error);
            }
        };

        return {
            user: props.user.id,
            dialog,
            filter,
            SaveFilter,
        };
    },
};
</script>
