<template>
    <div class="pa-4 text-left w-screen">
        <v-dialog v-model="dialog" max-width="600">
            <template v-slot:activator="{ props: activatorProps }">
                <v-fab v-if="!IsAllTab" :disabled="IsAllTab" class="position-fixed bottom-0 right-0 mb-28 mr-20 text-none font-weight-regular"
                    icon="mdi-pen" color="green" variant="outlined" v-bind="activatorProps"></v-fab>

            </template>

            <v-card>
                <v-card-title>
                    <v-icon left>mdi-pen</v-icon>
                   Edit Enroll
                </v-card-title>

                <v-card-text>

                    <v-form ref="formRef" v-model="valid">
                        <v-row dense>
                         <!--   <v-col cols="12" md="4" sm="6">
                                <v-text-field v-model="filter.target" label="Target"></v-text-field>
                            </v-col>
-->
                            <v-col cols="12" md="4" sm="6">
                                <v-text-field v-model="filter.nama_filter" label="Nama Filter*"
                                    :rules="[v => !!v || 'Nama Filter is required']" required></v-text-field>
                            </v-col>
                        </v-row>
                        <V-row>
                            <v-col cols="12" md="9" sm="9">
                                <v-textarea hint="1,2,3" v-model="filter.machine" label="Mesin"
                                    placeholder="Enter machine IDs"
                                    :rules="[v => !!v || 'Mesin is required']"></v-textarea>
                            </v-col>
                        </v-row>

                        <small class="text-caption text-medium-emphasis">
                            *indicates required field
                        </small>
                    </v-form>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn color="error" text @click="confirmDelete">
                        Delete
                    </v-btn>

                    <v-btn color="primary" text @click="UpdateFilter" :loading="isSaving">
                        Save
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>


        <v-dialog v-model="confirmDialog" max-width="400">
            <v-card>
                <v-card-title class="headline">Confirm Delete</v-card-title>
                <v-card-text>
                    Are you sure you want to delete this filter?
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="confirmDialog = false">Cancel</v-btn>
                    <v-btn color="red" text @click="DeleteFilter" :loading="isDeleting">
                        Delete
                    </v-btn>
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
            default: () => ({ nama_filter: '', machine: '' }),
        },
        UpdateDisplay: Function,
        handleTabChange: Function,
        IsAllTab: {
            type: Boolean,
            default: false,
        }
    },
    setup(props) {
        const dialog = ref(false);
        const confirmDialog = ref(false);
        const isSaving = ref(false);
        const isDeleting = ref(false);
        const valid = ref(false);
        const formRef = ref(null);

        const filter = reactive({
            nama_filter: '',
            machine: '',
            idUser: '',
            target: '',
        });

        watch(() => props.SelectedFilter, (newFilter) => {
            if (newFilter) {
                filter.id = newFilter.id || ''
                filter.nama_filter = newFilter.nama_filter || '';
                filter.machine = newFilter.machine || '';
            }
            else {
                console.log("IsAllTab:" + props.IsAllTab);
                filter.id = 9999;
                filter.nama_filter = "null";
                filter.machine = "null";
            }
        });

        const UpdateFilter = async () => {
            if (!formRef.value.validate()) return;

            isSaving.value = true;
            try {
                const Update = {
                    id: filter.id,
                    nama_filter: filter.nama_filter,
                    machine: Array.isArray(filter.machine) ? filter.machine.join(',') : filter.machine,
                    target: filter.target,
                };
                const response = await axios.put('/update-filter', Update);

                dialog.value = false;
                props.UpdateDisplay(response.data.enroll);
            } catch (error) {
                console.error('Failed:', error);
            } finally {
                isSaving.value = false;
            }
        };

        const confirmDelete = () => {
            confirmDialog.value = true;
        };

        const DeleteFilter = async () => {
            isDeleting.value = true;
            try {
                const id = filter.id;
                await axios.delete('/delete-filter', { params: { id } });

                confirmDialog.value = false;
                dialog.value = false;
                props.UpdateDisplay(null, filter.id);
                props.handleTabChange('all');
            } catch (error) {
                console.error('Failed:', error);
            } finally {
                isDeleting.value = false;
            }
        };

        return {
            dialog,
            confirmDialog,
            filter,
            valid,
            formRef,
            UpdateFilter,
            confirmDelete,
            DeleteFilter,
            isSaving,
            isDeleting,
        };
    },
};
</script>
