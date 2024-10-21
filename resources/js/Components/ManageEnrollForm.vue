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
                            <v-text-field v-model="filter.id" label="Nama Filter*" required></v-text-field>
                        </v-col>
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

                    <v-btn text="Delete" variant="plain" @click="DeleteFilter"></v-btn>

                    <v-btn color="primary" text="Save" variant="tonal" @click="UpdateFilter"></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
import axios from 'axios';
import { reactive, ref, watch } from 'vue';

export default {
    props: {
        SelectedFilter: {
            type: Object,
            default: () => ({ nama_filter: '', machine: '' })
        },
        UpdateDisplay: Function,
    },
    setup(props) {
        const dialog = ref(false);
        const filter = reactive({
            nama_filter: '',
            machine: '',
            idUser: '',
        });

        watch(() => props.SelectedFilter, (newFilter) => {
            filter.id = newFilter.id || ''
            filter.nama_filter = newFilter.nama_filter || '';
            filter.machine = newFilter.machine || '';
        });

        const UpdateFilter = async () => {
            try {
                const Update = {
                    id: filter.id,
                    nama_filter: filter.nama_filter,
                    machine: Array.isArray(filter.machine) ? filter.machine.join(',') : filter.machine,
                };
               const response = await axios.put('/update-filter', Update);

                dialog.value = false;
                props.UpdateDisplay(response.data.enroll);
            } catch (error) {
                console.error('Failed:', error);
            }
        };

        const DeleteFilter = async () => {
            try {
                const id = filter.id;

                console.log(id);
                await axios.delete('/delete-filter', { params: { id } });

                dialog.value = false;
                props.UpdateDisplay(null, filter.id);
            } catch (error) {
                console.error('Failed:', error);
            }
        }

        return {
            dialog,
            filter,
            UpdateFilter,
            DeleteFilter
        };
    },
};

</script>
