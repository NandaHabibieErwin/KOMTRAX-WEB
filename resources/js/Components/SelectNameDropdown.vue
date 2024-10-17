<template><v-select label="Select" :items="name" v-model="selected" variant="outlined" @change="emitSelection"></v-select></template>

<script>
import { LoadExcelFile } from '@/excel';
import axios from 'axios';

export default {
    data() {
        return {
            name: [],
            selected: null,
            isAdmin: false,
        };
    },
   async mounted() {
        await this.GetName();
        this.emitSelection();
    },
    watch: {
  selected() {
    this.LoadFile();  // Trigger when selection changes
  },
},
    methods: {
        emitSelection() {
      this.$emit('userSelected', this.selected); // Emit selected user
    },
        async GetName() {
            try {
                //const response = await axios.get('/getcustomername');
               // this.name = response.data;

                //if(page.props.auth.user.status == 'user'){}

                this.selected = "HASIL RIMBA INDONESIA" //this.name.length ? this.name[0] : null;
                this.emitSelection();
            } catch (error) {
                console.error('Failed to fetch usernames:', error);
            }
        },
        async LoadFile() {
            try {
                const fileData = await LoadExcelFile(this.selected);
                this.$emit('fileLoaded', fileData.fileUrl);
                console.log("SELECTED SUCCESS");
            } catch (error) {
                console.error('Failed to load excel file:', error);
            }
        },
    },
};
</script>
