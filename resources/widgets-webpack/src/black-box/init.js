import React from "react";
import ReactDOM from "react-dom";
import App from "./App";
import { config } from "../config";
const createReactApp = () => {
    const appContainer = document.createElement("div");
    appContainer.id = "app-container-q78er";
    document.body.append(appContainer);
    if (window.meta.product) {
        if (window.meta.product.type === "RingBuilderAdvance") {
            if (
                document.querySelector("product-form form[action='/cart/add']")
            ) {
                const productContainerClass = document.createElement("div");
                productContainerClass.id =
                    "ringBuilderAdvance-product-container-q78er";
                document
                    .querySelector("product-form form[action='/cart/add']")
                    .append(productContainerClass);
            } else {
                const productContainerClass = document.createElement("div");
                productContainerClass.id =
                    "ringBuilderAdvance-product-container-q78er";
                document
                    .querySelector("form[action='/cart/add']")
                    .append(productContainerClass);
            }
        }
    }

    const appElement = document.querySelector("#app-container-q78er");
    if (appElement) {
        ReactDOM.render(<App />, appElement);
    }
};

const createWidgetContainers = () => {
    Object.entries(config).forEach(([key, val]) => {
        const widgetContainer = document.createElement("div");
        widgetContainer.className = val.className;
        Object.entries(val.styles).forEach(([prop, style]) => {
            widgetContainer.style[prop] = style;
        });
        const target = document.querySelector(val.target);
        if (target) {
            target.prepend(widgetContainer);
        }
    });
};

export async function init() {
    createWidgetContainers();
    createReactApp();
}
