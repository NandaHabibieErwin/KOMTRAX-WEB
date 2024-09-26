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
            <UploadFile></UploadFile>
        </template>

        <div class="py-12">
            <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <UnitChart></UnitChart>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
