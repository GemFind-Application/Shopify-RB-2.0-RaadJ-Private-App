import React, { useCallback, useEffect, useState } from "react";
import {
    Button,
    Card,
    Layout,
    Frame,
    Toast,
    Banner,
    Link,
    List,
    Subheading,
    Heading,
    DataTable,
    Scrollable,
    Grid,
    ResourceList,
    Thumbnail,
    TextStyle,
    Tabs,
} from "@shopify/polaris";
import { saveAs } from "file-saver";
import Papa from "papaparse";
import axios from "axios";
import fileDownload from "js-file-download";
import SuccessfullImports from "./SuccessfullImports";
import FailedImports from "./FailedImports";

function CsvImport() {
    const [loading, setLoading] = useState(false);
    const [productImportLogs, setProductImportLogs] = useState([]);
    const [successProductArray, setSuccessProductArray] = useState([]);
    const [failedProductArray, setFailedProductArray] = useState([]);
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

    // State to store parsed data
    const [parsedData, setParsedData] = useState([]);

    //State to store the values
    const [values, setValues] = useState([]);

    const changeHandler = (event) => {
        Papa.parse(event.target.files[0], {
            header: true,
            skipEmptyLines: true,
            complete: function (results) {
                const rowsArray = [];
                const valuesArray = [];
                console.log(results);
                // Parsed Data Response in array format
                setParsedData(results.data);

                // Filtered Values
                setValues(results.data);
            },
        });
    };

    //CSV IMPORT API
    let handleCsvImportApi = async (e) => {
        try {
            let payLoad = {
                shopDomain: document.getElementById("shopOrigin").value,
                file: values,
            };
            setLoading(true);
            axios
                .post("/api/csvImportApi", {
                    data: payLoad,
                })
                .then((response) => {
                    console.log(response);
                    if (response.data.status == "success") {
                        setLoading(false);
                        setToastContent(response.data.message);
                        toggleActive();
                    } else {
                        setLoading(false);
                        setToastContent1(response.data.message);
                        toggleActive1();
                    }
                });
        } catch (err) {
            console.log(err);
        }
    };

    //GET LOGS API
    useEffect(() => {
        const getSuccessFileContent = async () => {
            const res = await fetch(
                "/api/getSuccessFileContent/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const successLogs = await res.json();
            setProductImportLogs(successLogs.data);
            setSuccessProductArray(successLogs.data.success_array);
            setFailedProductArray(successLogs.data.fail_array);
        };
        getSuccessFileContent();
    }, []);

    const rows = successProductArray.map((item) => {
        return [`${item.product_name}`, `${item.product_id}`, `${item.time}`];
    });

    const rows_2 = failedProductArray.map((item) => {
        return [`${item.product_name}`, `${item.product_id}`, `${item.time}`];
    });

    const [selected, setSelected] = useState(0);

    const handleTabChange = useCallback(
        (selectedTabIndex) => setSelected(selectedTabIndex),
        []
    );

    const tabs = [
        {
            id: "all-customers-fitted-2",
            content: "Successful Imports",
            accessibilityLabel: (
                <SuccessfullImports tabValue={selected} importRows={rows} />
            ),
            panelID: "all-customers-fitted-content-2",
        },
        {
            id: "accepts-marketing-fitted-2",
            content: "Failed Imports",
            accessibilityLabel: (
                <FailedImports tabValue={selected} importRows={rows_2} />
            ),
            panelID: "accepts-marketing-fitted-Ccontent-2",
        },
    ];

    let handlleRefresh = () => {
        const getSuccessFileContent = async () => {
            const res = await fetch(
                "/api/getSuccessFileContent/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const successLogs = await res.json();
            setProductImportLogs(successLogs.data);
            setSuccessProductArray(successLogs.data.success_array);
            setFailedProductArray(successLogs.data.fail_array);
        };
        getSuccessFileContent();
    };

    let handlleClearAll = () => {
        try {
            axios
                .post(
                    "/api/clearAllLogs/" +
                        document.getElementById("shopOrigin").value
                )
                .then((response) => {
                    if (response.data.status == "success") {
                        const getSuccessFileContent = async () => {
                            const res = await fetch(
                                "/api/getSuccessFileContent/" +
                                    document.getElementById("shopOrigin").value,
                                {
                                    method: "GET",
                                }
                            );
                            const successLogs = await res.json();
                            setProductImportLogs(successLogs.data);
                            setSuccessProductArray(
                                successLogs.data.success_array
                            );
                            setFailedProductArray(successLogs.data.fail_array);
                        };
                        getSuccessFileContent();
                        setToastContent(response.data.message);
                        toggleActive();
                    }
                });
        } catch (err) {
            console.log(err);
        }
    };

    return (
        <div style={{ height: "200px" }}>
            <Frame>
                <Layout>
                    <Layout.Section>
                        <Card title="Instructions to bulk upload or manual upload your settings">
                            <Card.Section>
                                <Heading element="h3">
                                    For CSV Bulk Upload
                                </Heading>
                                <List>
                                    <List.Item>
                                        If you want to add extra images you can
                                        add new column with Extra_image_1,
                                        Extra_image_2.
                                    </List.Item>
                                    <List.Item>
                                        If you want to add metafields then you
                                        can add metafileds_nameofyourattribute.
                                    </List.Item>
                                    <List.Item>
                                        If you want to add configurable product
                                        then you need to add configurable
                                        attribute in configurable column.
                                    </List.Item>
                                    <List.Item>
                                        In Product_Type column you need to fill
                                        the type of product for example simple
                                        or configurable.
                                    </List.Item>
                                    <List.Item>
                                        In Type column you need to fill
                                        "RingBuilderAdvance".
                                    </List.Item>
                                    <List.Item>
                                        Please download your sample file{" "}
                                        <a href={process.env.CSV_URL} download>
                                            here
                                        </a>
                                    </List.Item>
                                    <List.Item>
                                        To download installation guide -{" "}
                                        <a href={process.env.INSTALLATION_URL}>
                                            click here
                                        </a>
                                    </List.Item>
                                </List>
                            </Card.Section>
                            <Card.Section>
                                <Heading element="h3">
                                    To Add Setting Manually
                                </Heading>
                                <List>
                                    <List.Item>
                                        For creating manual products you need to
                                        add the following mandatory Metafields
                                        parameters: ringSize, Collections,
                                        shape, MinimumCarat, MaximumCarat,
                                        islabsettings
                                    </List.Item>
                                    <List.Item>
                                        For configurable products you need to
                                        add this mandatory parameter :
                                        MetalType,CenterStoneSize,
                                    </List.Item>
                                    <List.Item>
                                        In Product_Type column you need to fill
                                        the type of product for example simple
                                        or configurable.
                                    </List.Item>
                                    <List.Item>
                                        In Type column you need to fill
                                        "RingBuilderAdvance".
                                    </List.Item>
                                </List>
                            </Card.Section>
                            <Card.Section title="SELECT YOUR CSV FILE TO BULK PRODUCT UPLOAD:">
                                <Card.Subsection>
                                    <form>
                                        <input
                                            type={"file"}
                                            accept={".csv"}
                                            onChange={changeHandler}
                                            className="primary"
                                        />
                                    </form>
                                </Card.Subsection>
                            </Card.Section>
                            <Card.Section>
                                <Card.Subsection>
                                    <Button
                                        loading={loading}
                                        onClick={() => handleCsvImportApi()}
                                        primary
                                    >
                                        Save
                                    </Button>
                                    {toastMarkup}
                                    {toastMarkup1}
                                </Card.Subsection>
                            </Card.Section>
                        </Card>
                    </Layout.Section>
                </Layout>

                <div
                    className="ringbuilderadvancelogs"
                    style={{ paddingTop: "20px" }}
                >
                    <Layout>
                        <Layout.Section oneThird>
                            <Card title="Successful Imports">
                                <Card.Section>
                                    <TextStyle variation="positive">
                                        {productImportLogs.success_count}
                                    </TextStyle>
                                </Card.Section>
                            </Card>
                        </Layout.Section>
                        <Layout.Section oneThird>
                            <Card title="Failed Imports">
                                <Card.Section>
                                    <TextStyle variation="negative">
                                        {productImportLogs.fail_count}
                                    </TextStyle>
                                </Card.Section>
                            </Card>
                        </Layout.Section>
                    </Layout>
                </div>
                {/* LOGS */}
                <div style={{ paddingTop: "20px", paddingBottom: "50px" }}>
                    <Layout>
                        <Layout.Section>
                            <Card title="Logs">
                                <Card.Section>
                                    <a
                                        href="#"
                                        onClick={handlleClearAll}
                                        className="primary"
                                        style={{
                                            padding: "10px",
                                            backgroundColor: "red",
                                            color: "#fff",
                                            textDecoration: "none",
                                            position: "relative",
                                            float: "right",
                                            top: "-42px",
                                        }}
                                    >
                                        Clear All
                                    </a>
                                    <a
                                        href="#"
                                        onClick={handlleRefresh}
                                        className="primary"
                                        style={{
                                            padding: "10px",
                                            color: "#fff",
                                            backgroundColor: "#198754",
                                            textDecoration: "none",
                                            position: "relative",
                                            float: "right",
                                            top: "-42px",
                                            marginRight: "10px",
                                        }}
                                    >
                                        Refresh
                                    </a>
                                </Card.Section>
                                <Card.Section>
                                    <Tabs
                                        tabs={tabs}
                                        selected={selected}
                                        onSelect={handleTabChange}
                                        fitted
                                    >
                                        <Card.Section>
                                            {tabs[selected].accessibilityLabel}
                                        </Card.Section>
                                    </Tabs>
                                </Card.Section>
                            </Card>
                        </Layout.Section>
                    </Layout>
                </div>
            </Frame>
        </div>
    );
}

export default CsvImport;
