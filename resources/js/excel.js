import axios from "axios";
import * as XLSX from 'xlsx';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


export async function UploadExcelFile(file) {
    try {
      const formData = new FormData();
      formData.append('file', file, file.name);

      const response = await axios.post('/upload-excel', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      if (response.status !== 200) throw new Error('Failed to upload file');

      console.log('File uploaded:', file.name);
    } catch (error) {
      console.error('Error:', error);
      throw error;
    }
  }

export const LoadExcelFile = async () => {
    try {
        const response = await axios.get('/get-excel');

            return {
                fileUrl: response.data.filepath,
                data: response.data.data,
                filename: response.data.filename,

            }

    } catch (error) {
        console.error('Cannot load excel file', error);
        throw error;
    }
};


export async function processExcelFile(fileUrl, SelectedSeries) {
    try {
        const response = await fetch(fileUrl);
        const data = await response.arrayBuffer();
        const workbook = XLSX.read(data, { type: 'array' });

        const charts = [];
        workbook.SheetNames.forEach((sheetName, sheetIndex) => {
            if (sheetIndex <= 1) return; // Skip first two sheets
            const worksheet = workbook.Sheets[sheetName];
            const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

            const chartData = processData(jsonData, SelectedSeries);
            charts.push(chartData);
        });

        return charts;
    } catch (error) {
        console.error('Error processing Excel file:', error);
    }
}

function processData(jsonData, SelectedSeries) {
    const header = jsonData[0];
    const sortedData = jsonData.slice(1).sort((a, b) => {
        const dateA = new Date(a[7] || null);
        const dateB = new Date(b[7] || null);
        return dateA - dateB;
    });

    const labels = sortedData.map(row => {
        const dateStr = row[7] || '';
        const dateObj = new Date(dateStr || null);
        return dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });

    const WorkingHour = sortedData.map(row => row[5] || 0);
    const FuelConsumption = sortedData.map(row => row[12] || 0);
    const IdlingHour = sortedData.map(row => row[13] || 0);
    const PModeChart = sortedData.map(row => {
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

    return {
        options: {
            chart: {
                id: `Unit-Data-${header[0]}`,
                type: ['EMode', 'PMode'].includes(SelectedSeries) ? 'bar' : 'area',
            },
            xaxis: {
                categories: labels,
            },
            yaxis: {
                min: 0,
                max: 24,
                title: { text: 'Hours' },
            }
        },
        series: SelectedSeries === 'Working Hour' ? [
            { name: 'Working Hour', data: WorkingHour }
        ] : SelectedSeries === 'Fuel Consumption' ? [
            { name: 'Fuel Consumption', data: FuelConsumption }
        ] : SelectedSeries === 'Idling Ratio' ? [
            { name: 'Idling Ratio', data: IdlingHour }
        ] : SelectedSeries === 'PMode' ? [
            { name: 'PMode', data: PModeChart }
        ] : [],
        ChartTitle: header[1] + "-" + header[2],
        AdditionalTitle: "SMR: " + header[4] + " H"
    };
}
