<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UnitChart from '@/Components/ChartnCard/LineChart.vue';
import ActionButton from '@/Components/ActionButton.vue';
import UploadFile from '@/Components/UploadFile.vue';

</script>

<template>
    <Head title="Enroll" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Enroll</h2>
        </template>

        <div class="py-12">
            <div class="max-w-9xl mx-auto sm:px-4 lg:px-5">
                <div class="bg-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                        <v-sheet elevation="6">
                          <v-tabs
                            bg-color="indigo"
                            next-icon="mdi-arrow-right-bold-box-outline"
                            prev-icon="mdi-arrow-left-bold-box-outline"
                            show-arrows
                          >
                            <v-tab
                              v-for="i in 30"
                              :key="i"
                              :text="`Item ${i}`"
                            ></v-tab>
                          </v-tabs>
                        </v-sheet>

                    <UnitChart></UnitChart>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
