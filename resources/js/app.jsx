import "./bootstrap";
import React from "react";
import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";

const pages = import.meta.glob("./Pages/**/*.jsx", { eager: true });

const inertiaElement = document.getElementById("inertia-app");

createInertiaApp({
    id: "inertia-app",

    resolve: (name) => {

        const page = pages[`./Pages/${name}.jsx`];

        if (!page) {
            throw new Error(`Página não encontrada: ./Pages/${name}.jsx`);
        }

        return page.default;
    },

    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});