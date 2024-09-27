<style lang="css" module>
#chart {
    max-width: 760px;
    margin: 35px auto;
    opacity: 0.9;
}

.arrow_box {
    position: relative;
    background: #555;
    border: 2px solid #000000;
}

.arrow_box:after,
.arrow_box:before {
    right: 100%;
    top: 50%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
}

.arrow_box:after {
    border-color: rgba(85, 85, 85, 0);
    border-right-color: #555;
    border-width: 10px;
    margin-top: -10px;
}

.arrow_box:before {
    border-color: rgba(0, 0, 0, 0);
    border-right-color: #000000;
    border-width: 13px;
    margin-top: -13px;
}

#chart .apexcharts-tooltip {
    color: #fff;
    transform: translateX(10px) translateY(10px);
    overflow: visible !important;
    white-space: normal !important;
}

#chart .apexcharts-tooltip span {
    padding: 5px 10px;
    display: inline-block;
}
</style>

<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-pagination v-model="page" :length="TotalPages" total-visible="7"
                    @input="UpdatePaginatedCharts"></v-pagination>
            </v-col>
        </v-row>

        <Grid :charts="PaginatedCharts"></Grid>

    </v-container>
</template>

<script>
import * as XLSX from 'xlsx';
import { UploadExcelFile, LoadExcelFile } from '@/excel';
import Grid from '@/Components/ChartnCard/Grid.vue';

export default {
    components: {
        Grid,
    },
    data() {
        return {
            charts: [],
            PaginatedCharts: [],
            page: 1,
            itemsPerPage: 8,
        };
    },
    watch: {
        page() {
            this.UpdatePaginatedCharts();
        }
    },
    computed: {
        TotalPages() {
            return Math.ceil(this.charts.length / this.itemsPerPage);
        },
    },
    mounted() {

        this.GetExcel();
    },
    methods: {
        async GetExcel() {
            try {
                const response = await LoadExcelFile();
                this.fileUrl = response.fileUrl;
                this.filename = response.filename;

                await this.processFile(this.fileUrl);
            } catch (error) {
                console.error('Error fetching Excel file:', error);
            }
        },
        async processFile(fileUrl) {
            try {
                const response = await fetch(fileUrl);
                const data = await response.arrayBuffer();

                const workbook = XLSX.read(data, { type: 'array' });

                workbook.SheetNames.forEach((sheetName, sheetIndex) => {
                    const worksheet = workbook.Sheets[sheetName];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    console.log(jsonData);

                    const labels = jsonData.slice(1).map(row => row[7] || '');
                    const WorkingHour = jsonData.slice(1).map(row => row[5] || 0);
                    const ActualWorkingHour = jsonData.slice(1).map(row => row[6] || 0);

                    const UnitChart = {
                        options: {
                            chart: {
                                id: `Unit-Data-${sheetIndex}`,
                                toolbar: { show: false },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800,
                                },
                                zoom: {
                                    enabled: true,
                                    type: 'x',
                                    autoScaleYaxis: true,
                                }
                            },
                            xaxis: {
                                categories: labels,
                                tickAmount: 5,
                                hideOverlappingLabels: true,
                            },
                            yaxis: {
                                min: 0,
                                max: Math.max(...WorkingHour) + 5,
                                tickAmount: 4,
                            },
                            tooltip: {
                                enabled: true,
                                shared: true,
                                theme: 'light',
                                custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                                    return (
                                        '<div class="arrow_box">' +
                                        "<span>" +
                                        w.globals.labels[dataPointIndex] +
                                        ": " +
                                        series[seriesIndex][dataPointIndex] +
                                        "</span>" +
                                        "</div>"
                                    );
                                },
                                x: {
                                    show: false,
                                }
                            },
                            dataLabels: { enabled: false },
                            theme: {
                                monochrome: {
                                    enabled: true,
                                    color: '#FFD500',
                                    shadeTo: 'dark',
                                    shadeIntensity: 0.15,
                                },
                            },
                            stroke: { width: 2 },
                        },
                        series: [
                            {
                                name: 'Working Hour',
                                data: WorkingHour,
                            },
                            {
                                name: 'Actual Working Hour',
                                data: ActualWorkingHour,
                            },
                        ],
                    };

                    this.charts.push(UnitChart);
                });
                this.UpdatePaginatedCharts();
            } catch (error) {
                console.error('Error processing Excel file:', error);
            }
        },
        UpdatePaginatedCharts() {
            const start = (this.page - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            this.PaginatedCharts = this.charts.slice(start, end);
        }
    },
};
</script>
