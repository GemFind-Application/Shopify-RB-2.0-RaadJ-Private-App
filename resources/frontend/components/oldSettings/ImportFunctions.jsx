import {
    Button,
    CalloutCard,
    Card,
    FormLayout,
    Frame,
    List,
    RadioButton,
    Stack,
    Toast,
    SkeletonBodyText,
    SkeletonDisplayText,
    SkeletonPage,
    TextContainer,
    Layout,
} from "@shopify/polaris";
import React, { useCallback, useEffect, useState } from "react";
import SettingsForm from "./SettingsForm";

function ImportFunctions(props) {
    const [showTable, setShowTable] = useState();
    const [loading, setLoading] = useState(false);
    const [value, setValue] = useState("2");
    const [enableImport, setImport] = useState("");
    const [importType, setImportType] = useState();

    const handleChange = useCallback(
        (_checked, newValue) => setValue(newValue),
        []
    );
    //toast for success
    const [toastContent, setToastContent] = useState();
    const [toastActive, setToastActive] = useState(false);
    const toggleToastActive = () => {
        setToastActive(!toastActive);
    };
    const toggleActive = useCallback(
        () => setToastActive((toastActive) => !toastActive),
        []
    );
    const toastMarkup = toastActive ? (
        <Toast content={toastContent} onDismiss={toggleToastActive} />
    ) : null;

    //toast for error
    const [toastContent1, setToastContent1] = useState();
    const [toastActive1, setToastActive1] = useState(false);
    const toggleToastActive1 = () => {
        setToastActive1(!toastActive1);
    };
    //IMPORT API
    let handleImportApi = async (e) => {
        try {
            let payLoad = {
                shopDomain: document.getElementById("shopOrigin").value,
                value: "2",
                enableImport: enableImport,
            };
            setLoading(true);
            let response = await axios.post("/api/importApi", {
                data: payLoad,
            });
            // console.log(response);
            let type = 1;
            if (response.data.status == "success") {
                if (value === "2") {
                    location.reload();
                }
                setToastContent(response.data.message);
                setImportType(type);
                toggleActive();
            }
        } catch (err) {
            console.log(err);
        }
        setLoading(false);
    };

    useEffect(() => {
        //GET SETTINGS API
        const getSettingsData = async () => {
            const res = await fetch(
                "/api/getSettingsData/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const settingProduct = await res.json();
            setImportType(settingProduct.type_2);
            setShowTable(true);
        };
        getSettingsData();
    }, []);

    if (showTable === undefined) {
        return (
            <div>
                <Frame>
                    <Card>
                        <SkeletonPage primaryAction>
                            <Layout>
                                <Layout.Section>
                                    <Card sectioned>
                                        <SkeletonBodyText />
                                    </Card>
                                    <Card sectioned>
                                        <TextContainer>
                                            <SkeletonDisplayText size="small" />
                                            <SkeletonBodyText />
                                        </TextContainer>
                                    </Card>
                                </Layout.Section>
                            </Layout>
                        </SkeletonPage>
                    </Card>
                </Frame>
            </div>
        );
    } else {
        return <SettingsForm />
    }
}

export default ImportFunctions;
