import React, { useCallback, useEffect, useState } from "react";
import {
    Button,
    Card,
    FormLayout,
    RadioButton,
    Layout,
    SkeletonBodyText,
    SkeletonDisplayText,
    Frame,
    SkeletonPage,
    Stack,
    TextContainer,
    Toast,
    EmptyState,
    DropZone,
    Thumbnail,
    Caption,
    TextField,
} from "@shopify/polaris";
import CsvImport from "./csvImport";
import JsonImport from "./JsonImport";

function Import() {
    const [loading, setLoading] = useState(false);
    const [showTable, setShowTable] = useState();
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
    const toggleActive1 = useCallback(
        () => setToastActive1((toastActive1) => !toastActive1),
        []
    );
    const toastMarkup1 = toastActive1 ? (
        <Toast content={toastContent1} error onDismiss={toggleToastActive1} />
    ) : null;

    const [value, setValue] = useState("1");

    const handleChange = useCallback(
        (_checked, newValue) => setValue(newValue),
        []
    );
    const [enableImport, setImport] = useState("");

    const handleImportChange = useCallback(
        (_checked, newValueImport) => setImport(newValueImport),
        []
    );

    const [showImportType, setImportType] = useState([]);

    //GET IMPORT API
    useEffect(() => {
        const getImportData = async () => {
            const res = await fetch(
                "/api/getImportData/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const settingImport = await res.json();
            // console.log(settingImport);
            setImportType(settingImport);
            setShowTable(true);
        };
        getImportData();
    }, []);

    //IMPORT API
    let handleImportApi = async (e) => {
        try {
            let payLoad = {
                shopDomain: document.getElementById("shopOrigin").value,
                value: value,
                enableImport: enableImport,
            };
            setLoading(true);
            let response = await axios.post("/api/importApi", {
                data: payLoad,
            });
            // console.log(response);
            if (response.data.status == "success") {
                setToastContent(response.data.message);
                // console.log(response.data.data);
                if (response.data.data == "1") {
                    setImportType(1);
                }
                if (response.data.data == "2") {
                    setImportType(2);
                }
                if (response.data.data == "3") {
                    setImportType(3);
                }
                toggleActive();
            }
        } catch (err) {
            console.log(err);
        }
        setLoading(false);
    };

    // console.log(enableImport);
    // console.log(files);
    console.log(showImportType);
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
    }
    if (showImportType === 0) {
        if (value === "2") {
            return (
                <div style={{ height: "200px" }}>
                    <Frame>
                        {" "}
                        <Card title="Choose your Ring Settings upload options">
                            <Card.Section>
                                <p>
                                    Choosing JewelCloud API will populate the
                                    Ring Settings from your JewelCloud account
                                    with our default Ring settings providers. If
                                    you choose “Import” you have the option to
                                    upload in bulk or manually your own Ring
                                    Settings using a CSV spreadsheet or JSON
                                    data file.
                                </p>
                            </Card.Section>
                            <Card.Section title="Select Your Setting:">
                                <Card.Subsection>
                                    <FormLayout.Group>
                                        <Stack vertical>
                                            <RadioButton
                                                label="JC Api"
                                                helpText=""
                                                checked={value === "1"}
                                                id="1"
                                                name="accounts"
                                                onChange={handleChange}
                                            />
                                            <RadioButton
                                                label="Import"
                                                helpText=""
                                                id="2"
                                                name="accounts"
                                                checked={value === "2"}
                                                onChange={handleChange}
                                            />
                                        </Stack>
                                    </FormLayout.Group>
                                </Card.Subsection>
                                {/* <Card.Subsection> */}
                                {/* <FormLayout.Group> */}
                                {/* <RadioButton
                                            label="Bulk CSV Or Manual Product Upload"
                                            checked={enableImport === "csv"}
                                            id="csv"
                                            onChange={handleImportChange}
                                        /> */}
                                {/* <RadioButton
                                            label="Bulk JSON Or Manual Product Upload"
                                            id="json"
                                            checked={enableImport === "json"}
                                            onChange={handleImportChange}
                                        /> */}
                                {/* </FormLayout.Group> */}
                                {/* </Card.Subsection> */}
                            </Card.Section>
                            <Card.Section>
                                <Card.Subsection>
                                    <FormLayout.Group>
                                        <Button
                                            loading={loading}
                                            onClick={() => handleImportApi()}
                                            primary
                                        >
                                            Save
                                        </Button>
                                        {toastMarkup}
                                    </FormLayout.Group>
                                </Card.Subsection>
                            </Card.Section>
                        </Card>
                    </Frame>
                </div>
            );
        } else {
            return (
                <div style={{ height: "200px" }}>
                    <Frame>
                        {" "}
                        <Card title="Choose your Ring Settings upload options">
                            <Card.Section>
                                <p>
                                    Choosing JewelCloud API will populate the
                                    Ring Settings from your JewelCloud account
                                    with our default Ring settings providers. If
                                    you choose “Import” you have the option to
                                    upload in bulk or manually your own Ring
                                    Settings using a CSV spreadsheet or JSON
                                    data file.
                                </p>
                            </Card.Section>
                            <Card.Section title="Select Your Setting:">
                                <Card.Subsection>
                                    <FormLayout.Group>
                                        <Stack vertical>
                                            <RadioButton
                                                label="JC Api"
                                                helpText=""
                                                checked={value === "1"}
                                                id="1"
                                                name="accounts"
                                                onChange={handleChange}
                                            />
                                            <RadioButton
                                                label="Import"
                                                helpText=""
                                                id="2"
                                                name="accounts"
                                                checked={value === "2"}
                                                onChange={handleChange}
                                            />
                                        </Stack>
                                    </FormLayout.Group>
                                </Card.Subsection>
                            </Card.Section>
                            <Card.Section>
                                <Card.Subsection>
                                    <FormLayout.Group>
                                        <Button
                                            loading={loading}
                                            onClick={() => handleImportApi()}
                                            primary
                                        >
                                            Save
                                        </Button>
                                        {toastMarkup}
                                    </FormLayout.Group>
                                </Card.Subsection>
                            </Card.Section>
                        </Card>
                    </Frame>
                </div>
            );
        }
    } else {
        if (showImportType === 2) {
            return <CsvImport />;
        }
        if (showImportType === 3) {
            return <JsonImport />;
        }
        if (showImportType === 1) {
            return (
                <div style={{ height: "200px" }}>
                    <Layout>
                        <Layout.Section>
                            <Card sectioned>
                                <EmptyState
                                    image="https://cdn.shopify.com/s/files/1/0262/4071/2726/files/emptystate-files.png"
                                    fullWidth
                                >
                                    <p>
                                        Currently you are using api for your
                                        ring builder settings.
                                    </p>
                                </EmptyState>
                            </Card>
                        </Layout.Section>
                    </Layout>
                </div>
            );
        }
    }
}

export default Import;
