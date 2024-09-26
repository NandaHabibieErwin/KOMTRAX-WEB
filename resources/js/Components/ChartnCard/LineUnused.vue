<template>
    <div>

        <div v-for="(chartData, index) in limitedCharts" :key="index" class="chart-container">
            <h3>Chart for Row {{ index + 1 }}</h3>
            <apexchart
                width="350px"
                height="253px"
                type="area"
                :options="chartData.options"
                :series="chartData.series"
            ></apexchart>
        </div>

        <button v-if="showLoadMore" @click="loadMoreCharts">Load More Charts</button>
    </div>
</template>

<script>
import * as XLSX from 'xlsx';
import { UploadExcelFile, GetExcelFile, LoadExcelFile, GetAllExcelFiles } from '@/excel';
export default {
    data() {
        return {
            charts: [], // All charts
            visibleChartCount: 10, // Number of charts to render initially
            loadAmount: 10, // Number of charts to load per click
        };
    },
    computed: {
        limitedCharts() {
            // Return the limited number of charts based on visibleChartCount
            return this.charts.slice(0, this.visibleChartCount);
        },
        showLoadMore() {
            // Show the Load More button if there are more charts to load
            return this.visibleChartCount < this.charts.length;
        }
    },
    mounted() {

        this.fetchAllExcelFiles();
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

        async fetchAllExcelFiles() {
  try {
    const files = await GetAllExcelFiles(); // This might be a single object

    // Check if we got a single file object
    if (files && !Array.isArray(files)) {
      // If it's not an array, process it as a single file
      await this.processExcelFile(files.file_path, files.filename);
    } else if (Array.isArray(files) && files.length > 0) {
      // If it's an array, process each file
      for (const file of files) {
        await this.processExcelFile(file.file_path, file.filename);
      }
    } else {
      console.error('No files found');
    }
  } catch (error) {
    console.error('Error fetching all Excel files:', error);
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
        async processExcelFile(fileUrl, filename) {
    try {
        const response = await fetch(fileUrl);
        const data = await response.arrayBuffer();
        const workbook = XLSX.read(data, { type: 'array' });
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        if (!jsonData || jsonData.length === 0) {
            console.error('Invalid or empty Excel file');
            return;
        }

        // First row is the header (x-axis labels)
        const labels = jsonData[0];
        const newCharts = [];

        jsonData.slice(1).forEach((row, index) => {
            const WorkingHour = row[67] !== undefined ? row[67] : 0; // Ensure valid number
            const ActualWorkingHour = row[68] !== undefined ? row[68] : 0; // Ensure valid number
            const date = row[0] || `Unknown Date ${index + 1}`; // Ensure a date or a fallback

            if (!row || row.length === 0) {
                console.warn(`Skipping empty row ${index + 1}`);
                return;
            }

            // Prepare chart data
            const chartData = {
                filename,
                options: {
                    chart: {
                        id: `vuechart-${newCharts.length}`,
                        toolbar: { show: false },
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
                        categories: [date],  // X-axis displaying the date
                        labels: {
                            format: 'dd MMM',  // Display the date format as needed
                        },
                    },
                    yaxis: {
                        min: 0,
                        max: 24,  // 0-24 hour interval for y-axis
                        tickAmount: 6,  // 6 ticks (0, 4, 8, 12, 16, 20, 24)
                        title: {
                            text: 'Hours',
                        }
                    },
                    tooltip: {
                        enabled: true,
                        shared: true,
                        intersect: false,
                        theme: 'dark',
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
                        data: [WorkingHour],  // Valid numeric data for working hours
                    },
                    {
                        name: 'Actual Working Hour',
                        data: [ActualWorkingHour],  // Valid numeric data for actual working hours
                    }
                ],
            };

            newCharts.push(chartData);
        });

        // Append new charts
        this.charts.push(...newCharts);

    } catch (error) {
        console.error('Error processing Excel file:', error);
    }
},
        loadMoreCharts() {
            // Increase the visibleChartCount by loadAmount to load more charts
            this.visibleChartCount += this.loadAmount;
        }
    },
};
</script>
