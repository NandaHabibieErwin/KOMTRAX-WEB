// Vuetify
import "vuetify/styles";
import { createVuetify } from "vuetify";
import { aliases, mdi } from 'vuetify/iconsets/mdi';
import { VDateInput } from 'vuetify/labs/VDateInput';
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";

import '@mdi/font/css/materialdesignicons.css';

const customeTheme = {
    dark: false,
    colors: {
        primary: "#673AB7",
        secondary: "#424242",
        accent: "#82B1FF",
        error: "#FF5252",
        info: "#2196F3",
        success: "#4CAF50",
        warning: "#FFC107",
        lightblue: "#14c6FF",
        yellow: "#FFD500",
        pink: "#FF1976",
        orange: "#FF8657",
        magenta: "#C33AFC",
        darkblue: "#1E2D56",
        gray: "#909090",
        neutralgray: "#9BA6C1",
        green: "#2ED47A",
        red: "#FF5c4E",
        darkblueshade: "#308DC2",
        lightgray: "#f3f4f6",
        lightpink: "#FFCFE3",
        white: "#FFFFFF",
        muted: "#6c757d",
    },
};

const vuetify = createVuetify({
    components: {
        VDateInput,
        ...components,
    },
    directives,
    theme: {
        defaultTheme: "customeTheme",
        themes: {
            customeTheme,
        },
    },
    icons: {
        defaultSet: "mdi",
        aliases,
        sets: {
            mdi,
        },
    },
});

export default vuetify;
