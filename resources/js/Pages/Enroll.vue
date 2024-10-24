<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UnitChart from '@/Components/ChartnCard/LineChart.vue';
import EnrollForm from '@/Components/EnrollForm.vue';


</script>
<template>

    <Head title="Enroll" />

    <AuthenticatedLayout>
    <!--    <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Enroll</h2>
        </template> -->

        <div>
            <div class="max-w-9xl mx-1 sm:px-4 lg:px-5">
                <div class="bg-gray-100 overflow-hidden shadow-sm sm:rounded-lg">
                    <EnrollForm @filterSaved="addFilterToTabs"/>
                    <UnitChart class="mx-0 px-0 w-max"></UnitChart>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
