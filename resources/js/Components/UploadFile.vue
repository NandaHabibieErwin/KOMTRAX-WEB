<template>
    <div>

      <v-file-input
        v-model="files"
        label="File input"
        placeholder="Upload your documents"
        prepend-icon="mdi-paperclip"
        multiple
        required
        @change="handleFileUpload"
      >
        <template v-slot:selection="{ fileNames }">
          <template v-for="fileName in fileNames" :key="fileName">
            <v-chip class="me-2" color="primary" size="small" label>{{ fileName }}</v-chip>
          </template>
        </template>
      </v-file-input>

      <UploadButton
        :variant="'primary'"
        :size="'medium'"
        @click="uploadFiles"
      >
        Upload Files
      </UploadButton>
    </div>
  </template>

  <script>
  import UploadButton from './UploadButton.vue';
  import LineChart from './ChartnCard/LineChart.vue';
  export default {
    components: {
      UploadButton,
    },
    data() {
      return {
        files: [],
        Upload: false,
      };
    },
    methods: {

      handleFileUpload() {

        console.log('Files selected:', this.files);
      },
      async uploadFiles() {
        if (!this.files.length || this.Upload) return;

        this.Upload = true;

        try {

          for (let file of this.files) {
            await this.uploadFile(file);
          }

          console.log('Files successfully uploaded');

          this.files = [];
        } catch (error) {
          console.error('Error uploading files:', error);
        }
        finally {
            this.Upload = false;
        }
      },
      async uploadFile(file) {
        try {
          const formData = new FormData();
          formData.append('file', file, file.name);

          const response = await axios.post('/upload-excel', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      if (response.status !== 200) throw new Error('Failed to upload file');
          console.log('File uploaded:', file.name);
        } catch (error) {
          console.error('Error in uploadFile:', error);
        }
      },
    },
  };
  </script>

