<template>
    <v-btn
      :color="variant"
      :small="size === 'small'"
      :large="size === 'large'"
      :disabled="Upload"
      @click="handleClick"
    >
      <slot></slot>
    </v-btn>
  </template>

  <script>
  
  export default {
    name: 'UploadButton',
    props: {
      variant: { type: String, default: 'primary' },
      size: { type: String, default: 'medium' },
      disabled: { type: Boolean, default: false },
    },
    methods: {
      handleClick(event) {
        if (!this.disabled) {
          this.$emit('click', event);
        }
      },
    },
  };
  </script>
