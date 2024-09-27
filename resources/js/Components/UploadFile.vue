<template>
    <div>
        <v-row>
            <v-col>

                <v-file-input v-model="files" label="Click to select files..." placeholder="Upload your documents"
                    prepend-icon="mdi-paperclip" multiple required @change="handleFileUpload">
                    <template v-slot:selection="{ fileNames }">
                        <template v-for="fileName in fileNames" :key="fileName">
                            <v-chip class="me-2" color="primary" size="small" label>{{ fileName }}</v-chip>
                        </template>
                    </template>
                </v-file-input>
            </v-col>
            <v-col>
                <UploadButton :variant="'warning'" :size="'medium'" @click="uploadFiles">
                    Upload Files
                </UploadButton>
            </v-col>
        </v-row>
    </div>

</template>

<script>
import UploadButton from './UploadButton.vue';
import LineChart from './ChartnCard/LineChart.vue';
import { UploadExcelFile } from '@/excel';
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
                    await UploadExcelFile(file);
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
    },
};
</script>
