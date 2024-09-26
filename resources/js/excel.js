import axios from "axios";
import * as XLSX from 'xlsx';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


export const UploadExcelFile = async (file) => {
    const formData = new FormData();
    formData.append('file', file);

    try {
      const response = await axios.post('/upload-excel', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      return response.data;
    } catch (error) {
      console.error('Error uploading the file:', error);
      throw error;
    }
  };

  export const GetExcelFile = async (fileId) => {
    try {
        const response = await axios.get(`/get-excel/${fileId}`);
        return response.data;
    } catch (error) {
        console.error('Error fetching the Excel file:', error);
        throw error;
    }
};
  export const ReadExcelData = async (filePath) => {
    try {
        const response = await axios.get("/retrieve-data", {
            params: {
                filepath: filePath,
            },
        });
        return response.data;
    } catch (error) {
        console.error("Error reading the Excel file:", error);
        throw error;
    }
};

export const LoadExcelFile = async () => {
    try {
        const response = await axios.get('/retrieve');
        if (response.data.filepath) {
            return {
                fileUrl: response.data.filepath,
                fileId: response.data.fileid,
                filename: response.data.filename,
            };
        } else {
            throw new Error('No File Detected');
        }
    } catch (error) {
        console.error('Cannot find Excel File:', error);
        throw error;
    }
};
