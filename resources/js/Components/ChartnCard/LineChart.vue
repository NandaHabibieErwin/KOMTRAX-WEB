<template>
    <div>
        <apexchart width="350px" height="253px" type="area" :options="options" :series="series"></apexchart>
    </div>
</template>

<script>
import * as XLSX from 'xlsx';
import { UploadExcelFile, GetExcelFile, LoadExcelFile } from '@/excel';
export default {
    data() {
        return {
            options: {
                chart: {
                    id: 'vuechart-example',
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    },
                },
                xaxis: {
                    min: 1,
                    max: 30,
                    tickAmount: 5,
                    hideOverlappingLabels: true,
                },
                yaxis: {
                    show: true,
                    min: 0,
                    max: 24,
                    tickAmount: 4,
                },
                tooltip: {
                    enabled: true,
                    enabledOnSeries: true,
                    shared: true,
                    intersect: false,
                    theme: 'dark',
                    x: {
                        show: true,
                        format: 'dd MMM',
                    },
                    y: {
                        title: {
                            formatter: (seriesName) => seriesName,
                        },
                    },
                    fixed: {
                        enabled: true,
                        position: 'topRight',
                    },
                },
                dataLabels: {
                    enabled: false,
                },

                theme: {
                    monochrome: {
                        enabled: true,
                        color: '#FFD500',
                        shadeTo: 'dark',
                        shadeIntensity: 0.15
                    }
                },
                stroke: {
                    width: 2
                },
            },
            series: [
                {
                    name: 'Working Hour',
                    data: [],
                },
                {
                    name: 'Actual Working Hour',
                    data: [],
                },
            ],
        };
    },
    mounted() {

        this.fetchLatestExcelFile();
    },
    methods: {
        async handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            try {

                const response = await UploadExcelFile(file);
                const fileId = response.file_id;


                this.fetchExcelFileData(fileId);
            } catch (error) {
                console.error('Error uploading file:', error);
            }
        },
        async fetchExcelFileData(fileId) {
            try {
                const response = await GetExcelFile(fileId);
                this.fileUrl = response.file_path;
                this.filename = response.filename;


                this.ProcessExcelFile(this.fileUrl);
                updateChart(this, labels, values);
            } catch (error) {
                console.error('Error fetching Excel file:', error);
            }
        },
        async fetchLatestExcelFile() {
            try {
                const response = await LoadExcelFile();
                this.fileUrl = response.fileUrl;
                this.fileId = response.fileId;
                this.filename = response.filename;

                // Process the Excel file and update the chart
                await this.processExcelFile(this.fileUrl);
            } catch (error) {
                console.error('Error fetching the latest Excel file:', error);
            }
        },
        async processExcelFile(fileUrl) {
            try {
                const response = await fetch(fileUrl);


                const data = await response.arrayBuffer();

                const workbook = XLSX.read(data, { type: 'array' });

                const sheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[sheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                const labels = jsonData.slice(1).map(row => row[0]);
                const values = jsonData.slice(1).map(row => row[8]);
                console.log(labels);
                console.log(values);

                this.updateChart(labels, values); // Update the chart
            } catch (error) {
                console.error('Error processing Excel file:', error);
            }
        },
        updateChart(labels, values) {
            this.options.xaxis.categories = labels;
            this.series[0].data = values;
        },
    },
};
</script>
