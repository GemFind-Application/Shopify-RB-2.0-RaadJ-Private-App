import React from "react";
import ReactDOM from "react-dom";
import { CONST } from "./constants";
import { config } from "../config";
import BreadCrumbUpper from "../elements/BreadCrumbUpper";
import ProductContainer from "../components/ProductDetail/ProductContainer";

const { TEST_BAR } = CONST;

const App = () => {
    const collectionExists = window.location.href;
    var collection = collectionExists.search("ringbuilder-settings");
    const testBar = document.querySelector("." + config[TEST_BAR].className);
    const addProductClass = document?.getElementById(
        "ringBuilderAdvance-product-container-q78er"
    );
    if (window.meta.page.pageType === "collection") {
        if (collection !== -1) {
            return (
                <>
                    {testBar &&
                        ReactDOM.createPortal(<BreadCrumbUpper />, testBar)}
                </>
            );
        }
    }
    if (window.meta.page.pageType === "product") {
        console.log("hi");
        if (window.meta.product.type === "RingBuilderAdvance") {
            return (
                <>
                    {testBar &&
                        ReactDOM.createPortal(<BreadCrumbUpper />, testBar)}
                    {addProductClass &&
                        ReactDOM.createPortal(
                            <ProductContainer />,
                            addProductClass
                        )}
                </>
            );
        } else {
            return null;
        }
    } else {
        return null;
    }
};

export default App;
