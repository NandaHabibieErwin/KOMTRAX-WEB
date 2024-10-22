<template>
    <div class="pa-4 text-center">
        <!-- Improved Dialog -->
        <v-dialog v-model="dialog" max-width="600" persistent>
            <template v-slot:activator="{ props: activatorProps }">
                <v-btn
                    v-if="!IsAllTab"
                    :disabled="IsAllTab"
                    class="text-none font-weight-regular"
                    prepend-icon="mdi-account"
                    variant="tonal"
                    v-bind="activatorProps"
                >
                    Edit Profile
                </v-btn>
            </template>

            <v-card>
                <v-card-title>
                    <v-icon left>mdi-account</v-icon>
                    User Profile
                </v-card-title>

                <v-card-text>
                    <!-- Add ref to v-form -->
                    <v-form ref="formRef" v-model="valid">
                        <v-row dense>
                            <v-col cols="12" md="4" sm="6">
                                <v-text-field v-model="filter.target" label="Target"></v-text-field>
                            </v-col>

                            <v-col cols="12" md="4" sm="6">
                                <v-text-field
                                    v-model="filter.nama_filter"
                                    label="Nama Filter*"
                                    :rules="[v => !!v || 'Nama Filter is required']"
                                    required
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" md="4" sm="6">
                                <v-text-field
                                    hint="1,2,3"
                                    v-model="filter.machine"
                                    label="Mesin"
                                    placeholder="Enter machine IDs"
                                    :rules="[v => !!v || 'Mesin is required']"
                                ></v-text-field>
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

        <!-- Confirmation Dialog for Deletion -->
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

### Updated Script:
```javascript
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
        IsAllTab: {
            type: Boolean,
            default: false,
        },
    },
    setup(props) {
        const dialog = ref(false);
        const confirmDialog = ref(false);
        const isSaving = ref(false);
        const isDeleting = ref(false);
        const valid = ref(false);

        // Reference to the v-form
        const formRef = ref(null);

        const filter = reactive({
            nama_filter: '',
            machine: '',
            idUser: '',
            target: '',
        });

        watch(
            () => props.SelectedFilter,
            (newFilter) => {
                if (!props.IsAllTab && newFilter) {
                    filter.id = newFilter.id || '';
                    filter.nama_filter = newFilter.nama_filter || '';
                    filter.machine = newFilter.machine || '';
                    filter.target = newFilter.target || '';
                } else {
                    filter.id = 9999;
                    filter.nama_filter = 'null';
                    filter.machine = 'null';
                    filter.target = 'null';
                }
            },
            { immediate: true }
        );

        const UpdateFilter = async () => {
            // Ensure form validation is triggered correctly
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
            formRef, // Return the formRef
            UpdateFilter,
            confirmDelete,
            DeleteFilter,
            isSaving,
            isDeleting,
        };
    },
};
</script>
