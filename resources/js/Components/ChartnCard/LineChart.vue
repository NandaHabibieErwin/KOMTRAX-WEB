<template>
    <v-container @tab-status-changed="UpdateTabStatus">

        <div class="w-full m-0 p-0" style="width: 100vw; margin-top:0; padding-top:0;">
            <v-sheet elevation="4">
            </v-sheet>

            <v-sheet elevation="6">
                <v-tabs bg-color="red" next-icon="mdi-arrow-right-bold-box-outline"
                    prev-icon="mdi-arrow-left-bold-box-outline" show-arrows>
                    <v-tab key="all" @click="handleTabChange('all')">All</v-tab>
                    <v-tab v-for="(filter, i) in filters" :key="i" :text="filter.nama_filter"
                        @click="handleTabChange(i)"></v-tab>
                </v-tabs>
            </v-sheet>

            <v-sheet elevation="6">
                <v-tabs align-tabs="center" v-model="SelectedSeriesTab">
                    <v-tab>Working Hours</v-tab>
                    <v-tab>Fuel Consumption</v-tab>
                    <v-tab>Idling Ratio</v-tab>
                    <v-tab>E Mode</v-tab>
                    <v-tab>P Mode</v-tab>
                </v-tabs>

            </v-sheet>

        </div>
        <div>
            <v-progress-circular v-if="Loading" indeterminate color="primary"
                class=" mt-4 pt-4 my-3"></v-progress-circular>
            <div v-if="!Loading" class="mt-2 pt-2">
                <v-row>
                    <v-col cols="5" sm="3" md="2">
                        <v-text-field v-model="startDate" label="Start Date" variant="outlined" type="date"
                            @change="filterDate"></v-text-field>
                    </v-col>
                    <v-col cols="5" sm="3" md="2">
                        <v-text-field v-model="endDate" label="End Date" variant="outlined" type="date"
                            @change="filterDate"></v-text-field>
                    </v-col>
                </v-row>
            </div>
            <EnrollCard v-if="!Loading" :averageFuelConsumption="averageFuelConsumption"
                :averageIdlingRatio="averageIdlingRatio" :averagePModeRatio="averagePModeRatio" />

            <div class="w-screen">
                <Grid v-if="!Loading" :charts="PaginatedCharts" :SelectedSeries="SelectedSeries"></Grid>
            </div>
        </div>


        <v-row v-if="!Loading">
            <v-col cols="12">
                <v-pagination class="pagination mb-2" v-model="page" :length="TotalPages" total-visible="7"
                    @input="UpdatePaginatedCharts"></v-pagination>
            </v-col>
        </v-row>



        <EnrollForm :handleTabChange="handleTabChange" :filters="filters" :UpdateDisplay="UpdateDisplay" />
        <ManageEnrollForm :SelectedFilter="SelectedFilter" :UpdateDisplay="UpdateDisplay"
            :handleTabChange="handleTabChange" :IsAllTab="IsAllTab" />




    </v-container>
</template>


<script>
import * as XLSX from 'xlsx';
import { UploadExcelFile, LoadExcelFile } from '@/excel';
import Grid from '@/Components/ChartnCard/Grid.vue';
import ManageEnrollForm from '@/Components/ManageEnrollForm.vue';
import EnrollCard from './EnrollCard.vue';
import EnrollForm from '../EnrollForm.vue';

export default {
    components: {
        Grid,
        ManageEnrollForm,
        EnrollCard,
        EnrollForm
    },
    data() {
        return {
            filters: [],
            SelectedFilter: null,
            charts: [],
            PaginatedCharts: [],
            page: 1,
            itemsPerPage: 9,
            SelectedMachine: [],
            SelectedTab: 0,
            IsAllTab: false,
            SelectedSeriesTab: 0,
            SelectedSeries: 'Working Hour',
            averageFuelConsumption: null,
            averageIdlingRatio: null,
            averagePModeRatio: null,
            Loading: false,
            startDate: null,
            endDate: null,
        };
    },
    watch: {
        page() {
            this.UpdatePaginatedCharts();
        },
        SelectedSeriesTab(newTab) {
            this.updateSeries(newTab);
        },
    },
    computed: {
        TotalPages() {
            return Math.ceil(this.charts.length / this.itemsPerPage);
        },
    },
    mounted() {

        this.GetExcel();
        this.GetFilter();
        this.handleTabChange('all');
    },
    methods: {
        filterDate() {
            if (this.startDate && this.endDate) {
                console.log("Filtering between", this.startDate, "and", this.endDate);
                this.processFile(this.fileUrl); // Ensure fileUrl is available here
                this.UpdatePaginatedCharts();
            } else {
                console.log("Showing all data as startDate or endDate is missing");
                this.processFile(this.fileUrl); // Process without filtering if dates are not set
                this.UpdatePaginatedCharts();
            }
        },
        addFilterToTabs(newFilter) {
            this.filters.push(newFilter);
            this.SelectedFilter = newFilter;
            this.SetMachineFilter(newFilter.machine);
        },
        SetMachineFilter(machine) {
            if (machine === 'all') {
                this.SelectedMachine = [];

            }
            else if (typeof machine === 'string') {
                this.SelectedMachine = machine.split(',').map(m => m.trim());
            } else if (Array.isArray(machine)) {
                this.SelectedMachine = machine;
            } else {
                this.SelectedMachine = [];
            }
            this.UpdatePaginatedCharts();
        },
        async GetFilter() {
            try {
                const response = await axios.get('/read-enroll');
                this.filters = response.data.filters;
                if (this.filters.length > 0) {

                    // this.SetMachineFilter(all);
                }

            }
            catch (error) {
                console.error("Error:", error);
            }

        },
        handleTabChange(tabIndex) {
            this.Loading = true;
            if (tabIndex === 'all') {
                this.SetMachineFilter('all');
                this.SelectedFilter = null;
                this.IsAllTab = true;
                this.page = 1;
            } else {
                const filter = this.filters[tabIndex];
                this.SetMachineFilter(filter.machine);
                this.IsAllTab = false;
                this.SelectedFilter = filter;
                this.page = 1;
            }
            this.UpdatePaginatedCharts()
            this.Loading = false;

        },
        UpdateDisplay(updatedFilter, deletedId = null) {
            if (deletedId) {
                this.filters = this.filters.filter(f => f.id !== deletedId);
            } else if (updatedFilter) {

                const index = this.filters.findIndex(f => f.id === updatedFilter.id);
                if (index > -1) {
                    this.filters.splice(index, 1, updatedFilter);
                } else {
                    this.filters.push(updatedFilter);
                }
                this.filters = [...this.filters];

            }
        },
        async GetExcel() {
            this.Loading = true;
            try {
                const response = await LoadExcelFile();
                this.fileUrl = response.fileUrl;
                this.filename = response.filename;

                await this.processFile(this.fileUrl);
            } catch (error) {
                console.error("Error:", error);
            } finally {
                this.Loading = false;
            }

        },
        async processFile(fileUrl) {
            try {
                const response = await fetch(fileUrl);
                const data = await response.arrayBuffer();

                const workbook = XLSX.read(data, { type: 'array' });
                this.charts = [];
                workbook.SheetNames.forEach((sheetName, sheetIndex) => {
                    if (sheetIndex < -9) return;
                    const worksheet = workbook.Sheets[sheetName];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    const header = jsonData[0];

                    const filteredData = jsonData.filter(row => {
                        const dateStr = row[7] || '';
                        const dateObj = new Date(dateStr);
                        if (this.startDate && this.endDate) {
                            console.log("filtered" + this.startDate + "To" + this.endDate);
                            return dateObj >= new Date(this.startDate) && dateObj <= new Date(this.endDate);

                        }
                        console.log("all or filtered");

                        return true;
                    });
                    console.log(filteredData);
                    const sortedData = filteredData.sort((a, b) => {
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
                    const Model = sortedJsonData.slice(1).map(row => row[2] || 0).slice(-1);
                    const SerialNumber = sortedJsonData.slice(1).map(row => row[4] || 0).slice(-1);
                    //const FilterSerialNumber = sortedJsonData[1][4] || '';
                    const FilterSerialNumber = sortedJsonData[1]?.[4] || '';
                    const SMR = sortedJsonData.slice(1).map(row => row[11] || 0).slice(-1);
                    const FuelConsumption = sortedJsonData.slice(1).map(row => row[12] || 0);
                    const IdlingHour = sortedJsonData.slice(1).map(row => row[13] || 0);
                    const AverageFuelConsumption = (FuelConsumption.reduce((a, b) => a + b, 0) / FuelConsumption.length).toFixed(2);
                    const EModeActualWorkingHour = sortedJsonData.slice(1).map(row => row[14] || 0).filter(val => !isNaN(val)).filter(val => !isNaN(val) && val > 0);
                    const TotalEModeActualWorkingHour = (EModeActualWorkingHour.reduce((a, b) => a + b, 0));
                    const ActualWorkingHourToFilter = ActualWorkingHour.filter(val => !isNaN(val) && val > 0);
                    const TotalActualWorkingHour = ActualWorkingHour.reduce((a, b) => a + b, 0);
                    const PMode = ((TotalActualWorkingHour - TotalEModeActualWorkingHour) / TotalActualWorkingHour) * 100;
                    const EMode = sortedJsonData.slice(1).map(row => {
                        const rowWorkingHour = row[5] || 0;
                        const rowActualWorkingHour = row[6] || 0;
                        const rowEModeActualWorkingHour = row[14] || 0;
                        if (!isNaN(rowEModeActualWorkingHour) && rowEModeActualWorkingHour > 0) {
                            if (rowWorkingHour === 0 || rowActualWorkingHour === 0) return 0
                            else return ((rowEModeActualWorkingHour / rowActualWorkingHour) * 100).toFixed(2)
                        }
                        else return 0;
                    });
                    const PModeChart = sortedJsonData.slice(1).map(row => {
                        const rowWorkingHour = row[5] || 0;
                        const rowActualWorkingHour = row[6] || 0;
                        const rowEModeActualWorkingHour = row[14] || 0;
                        if (!isNaN(rowEModeActualWorkingHour) && rowEModeActualWorkingHour > 0) {
                            if (rowWorkingHour === 0 || rowActualWorkingHour === 0) return 100;
                            else {
                                const EModePercentage = (rowEModeActualWorkingHour / rowActualWorkingHour) * 100;
                                const PModePercentage = (100 - EModePercentage);
                                return PModePercentage.toFixed(2);
                            }
                        } else {
                            return 100;
                        }
                    });

                    const sumWorkingHour = WorkingHour.reduce((a, b) => a + b, 0);
                    const sumActualWorkingHour = ActualWorkingHour.reduce((a, b) => a + b, 0);

                    const JobEfficiency = ((1 - (((sumWorkingHour) - (sumActualWorkingHour)) / (sumWorkingHour))) * 100).toFixed(2);
                    const ChartTitle = Model + "-" + CustomerMachine + "-" + SerialNumber;
                    const AdditionalTitle = "SMR: " + SMR + " H<br>Fuel: " + AverageFuelConsumption + " " + "L/H<br>PMode: " + PMode.toFixed(1) + "%<br>Job Efficiency: " + JobEfficiency + "%";


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
                                },
                                type: ['EMode', 'PMode'].includes(this.SelectedSeries) ? 'bar' : 'area',
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
                            yaxis: this.SelectedSeries === 'Working Hour'
                                ? {
                                    min: 0,
                                    max: 24,
                                    tickAmount: 1,
                                    title: {
                                        text: 'Hours',
                                    },
                                }
                                : this.SelectedSeries === 'Fuel Consumption'
                                    ? {
                                        min: 0,
                                        tickAmount: 10,
                                        title: {
                                            text: 'Fuel Consumption (L/H)',
                                        },
                                    }
                                    : {
                                        min: 0,
                                        max: 100,
                                        tickAmount: 1,
                                        title: {
                                            text: 'Idling Ratio (%)',
                                        },
                                    },
                            annotations: {
                                yaxis: [{
                                    y: 50,
                                    borderColor: '#FF0000',
                                    label: {
                                        borderColor: '#FF0000',
                                        style: {
                                            color: '#fff',
                                            background: '#FF0000'
                                        },
                                    }
                                }]
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
                        series: this.SelectedSeries === 'Working Hour'
                            ? [
                                { name: 'Working Hour', data: WorkingHour },
                                { name: 'Actual Working Hour', data: ActualWorkingHour }
                            ]
                            : this.SelectedSeries === 'Fuel Consumption'
                                ? [
                                    { name: 'Fuel Consumption', data: FuelConsumption }
                                ]
                                : this.SelectedSeries === 'Idling Ratio'
                                    ? [
                                        { name: 'Idling Ratio', data: IdlingHour }
                                    ]
                                    : this.SelectedSeries === 'EMode'
                                        ? [
                                            { name: 'EMode', data: EMode }
                                        ]
                                        : this.SelectedSeries === 'PMode'
                                            ? [
                                                { name: 'PMode', data: PModeChart }
                                            ]
                                            : [],
                        ChartTitle: ChartTitle,
                        AdditionalTitle: AdditionalTitle,
                        FilterSerialNumber: FilterSerialNumber,
                    };

                    this.charts.push(UnitChart);
                });
                this.UpdatePaginatedCharts();
            } catch (error) {
                console.error('Error processing Excel file:', error);
            }
        },
        updateSeries(newTab) {
            this.Loading = true;
            if (newTab === 0) {
                this.SelectedSeries = 'Working Hour';
            } else if (newTab === 1) {
                this.SelectedSeries = 'Fuel Consumption';
            } else if (newTab === 2) {
                this.SelectedSeries = 'Idling Ratio';
            } else if (newTab === 3) { this.SelectedSeries = 'EMode'; }
            else if (newTab === 4) { this.SelectedSeries = 'PMode'; }
            console.log(this.SelectedSeries);
            this.charts = [];
            this.processFile(this.fileUrl).finally(() => {
                this.Loading = false;
            });
        },
        UpdatePaginatedCharts() {
            this.Loading = true;
            const filtered = this.IsAllTab
                ? this.charts
                : this.charts.filter(chart => {
                    return chart.FilterSerialNumber && this.SelectedMachine.some(machine => chart.FilterSerialNumber.includes(machine));
                });;

            this.filteredCharts = filtered;

            const start = (this.page - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;

            this.PaginatedCharts = this.filteredCharts.slice(start, end);

            this.GetAverage(filtered);
            this.Loading = false;
        },
        GetAverage(charts) {
            let validFuelConsumption = [];
            let validIdlingRatio = [];
            let validPModeRatio = [];

            charts.forEach(chart => {
                if (chart.series && chart.series.length > 0) {
                    chart.series.forEach(series => {
                        if (series.name === 'Fuel Consumption') {
                            validFuelConsumption.push(...series.data.filter(val => !isNaN(val) && val > 0));
                        }
                        if (series.name === 'Idling Ratio') {
                            validIdlingRatio.push(...series.data.filter(val => !isNaN(val) && val > 0));
                        }
                        if (series.name === 'PMode') {
                            validPModeRatio.push(...series.data.filter(val => !isNaN(val) && val > 0));
                        }
                    });
                }
            });


            this.averageFuelConsumption = validFuelConsumption.length > 0
                ? (validFuelConsumption.reduce((a, b) => a + b, 0) / validFuelConsumption.length).toFixed(2)
                : 0;

            this.averageIdlingRatio = validIdlingRatio.length > 0
                ? (validIdlingRatio.reduce((a, b) => a + b, 0) / validIdlingRatio.length).toFixed(2)
                : 0;

            this.averagePModeRatio = validPModeRatio.length > 0
                ? (
                    validPModeRatio
                        .map(val => parseFloat(val))
                        .filter(val => !isNaN(val))
                        .reduce((a, b) => a + b, 0) / validPModeRatio.length
                ).toFixed(2)
                : 0;

            console.log("average: " + this.averagePModeRatio);
            console.log("valid: " + validPModeRatio);

            console.log("Averages - Fuel Consumption:", this.averageFuelConsumption, "Idling Ratio:", this.averageIdlingRatio, "PMode Ratio:", this.averagePModeRatio);
        }

    },
};
</script>
