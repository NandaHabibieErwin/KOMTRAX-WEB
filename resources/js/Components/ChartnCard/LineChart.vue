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
                    if (sheetIndex === 0) return;
                    const worksheet = workbook.Sheets[sheetName];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });


                    const header = jsonData[0];


                    const sortedData = jsonData.slice(1).sort((a, b) => {
                        const dateA = a[7] ? new Date(a[7]) : null;
                        const dateB = b[7] ? new Date(b[7]) : null;

                        if (dateA && dateB) {
                            return dateA - dateB;
                        }

                        return dateA ? 1 : (dateB ? -1 : 0);
                    });


                    const sortedJsonData = [header, ...sortedData];


                    //                    console.log(sortedJsonData);

                    const labels = sortedJsonData.slice(1).map(row => {
                        const dateStr = row[7] || '';
                        const dateObj = dateStr ? new Date(dateStr) : null;
                        if (dateObj) {
                            return dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        }
                        return '';

                    });
                    const WorkingHour = sortedJsonData.slice(1).map(row => row[5] || 0);
                    const ActualWorkingHour = sortedJsonData.slice(1).map(row => row[6] || 0);
                    const CustomerMachine = sortedJsonData.slice(1).map(row => row[10] || 0).slice(-1);
                    const Type = sortedJsonData.slice(1).map(row => row[3] || 0);
                    const SerialNumber = sortedJsonData.slice(1).map(row => row[4] || 0).slice(-1);
                    const SMR = sortedJsonData.slice(1).map(row => row[11] || 0).slice(-1);
                    const FuelConsumption = sortedJsonData.slice(1).map(row => row[12] || 0);
                    const AverageFuelConsumption = (FuelConsumption.reduce((a, b) => a + b, 0) / FuelConsumption.length).toFixed(2);
                    const data = sortedJsonData.slice(1).map(row => {

                        if (!row[5] && !row[6]) {
                            return null;
                        } else if ((row[5] && row[6]) && !row[14]) {
                            return 0; //
                        } else {
                            return row[12] != null ? row[12] : 0;
                        }
                    });

                    const validData = data.filter(value => value !== null);

                    const ERatio = validData.length ? (validData.reduce((a, b) => a + b, 0) / validData.length).toFixed(2) : null;

                    let PRatio;
                    if (ERatio === null) {
                        PRatio = 0;
                    } else if (ERatio == 0) {
                        PRatio = 100;
                    } else {
                        PRatio = 100 - ERatio;
                    }

                    const ChartTitle = CustomerMachine + "-" + SerialNumber;
                    const AdditionalTitle = "SMR: " + SMR + " H\tFuel: " + AverageFuelConsumption + " " + "L/H" + PRatio.toFixed(2);

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
                            title: {
                                text: ChartTitle,
                                align: 'left',
                                margin: 10,
                                offsetX: 0,
                                offsetY: 0,
                                floating: false,
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    fontFamily: undefined,
                                    color: '#263238'
                                },
                            },
                            title: {
                                text: ChartTitle,
                                align: 'left',
                                margin: 10,
                                offsetX: 0,
                                offsetY: 0,
                                floating: false,
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    fontFamily: undefined,
                                    color: '#263238'
                                },
                            },
                            subtitle:
                            {
                                text: AdditionalTitle,
                                align: 'right',
                                margin: 10,
                                offsetX: 0,
                                offsetY: 0,
                                floating: false,
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 'normal',
                                    fontFamily: undefined,
                                    color: '#9699a2'
                                },
                            },
                            xaxis: {
                                categories: labels,
                                tickAmount: 5,
                                hideOverlappingLabels: true,
                            },
                            theme: {
                                monochrome: {
                                    enabled: true,
                                    color: '#FFD500',
                                    shadeTo: 'dark',
                                    shadeIntensity: 0.15,
                                },
                            },
                            yaxis: {
                                min: 0,
                                max: 24,//Math.max(...WorkingHour) + 5,
                                tickAmount: 1,
                            },
                            tooltip: {
                                enabled: true,
                                shared: true,
                                theme: 'light',
                                intersect: false,
                                x: {
                                    show: false,
                                }
                            },
                            dataLabels: { enabled: false },

                            stroke: { width: 3, curve: 'smooth' },
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
