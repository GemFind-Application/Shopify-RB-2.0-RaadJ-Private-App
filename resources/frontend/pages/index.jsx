import { AppProvider } from "@shopify/polaris";
import React from "react";
import ReactDOM from "react-dom";
import enTranslations from "@shopify/polaris/locales/en.json";
import CssBaseline from "@mui/material/CssBaseline";
import AppFrame from "../components/Routing/AppFrame";

function Index() {
    const config = {
        apiKey: document.getElementById("apiKey").value,
        shopOrigin: document.getElementById("shopOrigin").value,
        forceRedirect: true,
    };
    return (
        <AppProvider
            config={config}
            i18n={enTranslations}
            theme={{ colorScheme: "light" }}
        >
            <CssBaseline />
            <AppFrame></AppFrame>
        </AppProvider>
    );
}

export default Index;

if (document.getElementById("root")) {
    ReactDOM.render(<Index />, document.getElementById("root"));
}
