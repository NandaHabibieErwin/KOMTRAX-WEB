<script>
export default {
  data: () => ({
    hidden: false,
  }),
  props: {
    hidden: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    triggerAction() {
      this.$refs.fileInput.click();
    },
    handleFileUpload(event) {
      this.$emit("file-upload", event.target.files[0]);
    },
  },
};
</script>

<template>
  <v-fab
    :active="!hidden"
    class="me-4"
    icon="mdi-plus"
    location="top end"
    absolute
    offset
    @click="triggerAction"
  ></v-fab>

  <input type="file" ref="fileInput" @change="handleFileUpload" style="display: none;" />
</template>

<style scoped>
</style>
