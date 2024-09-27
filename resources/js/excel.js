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
        if (response.data.filepath && response.data.data) {
            return {
                fileUrl: response.data.filepath,
                data: response.data.data,
                filename: response.data.filename,
            };
        } else {
            throw new Error('No File Detected');
        }
    } catch (error) {
        console.error('Cannot load excel file', error);
        throw error;
    }
};
