import React, { useCallback, useEffect, useState } from "react";
import {
    Button,
    Card,
    FormLayout,
    Layout,
    SkeletonBodyText,
    SkeletonDisplayText,
    Frame,
    SkeletonPage,
    Stack,
    Toast,
    Banner,
    Link,
    List,
    Heading,
} from "@shopify/polaris";
import { saveAs } from "file-saver";

function JsonImport() {
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
    // State to store parsed data
    const [parsedJsonData, setParsedJsonData] = useState([]);

    //State to store the values
    const [jsonValues, setJsonValues] = useState([]);

    const changeJsonHandler = (event) => {
        var importedFile = event.target.files[0];

        var reader = new FileReader();
        reader.onload = function () {
            let fileContent = JSON.parse(reader.result);
            console.log(fileContent);
            setParsedJsonData(reader.result);
            setJsonValues(reader.result);
        };
        reader.readAsText(importedFile);
    };
    //JSON IMPORT API
    let handleJsonImportApi = async (e) => {
        try {
            let payLoad = {
                shopDomain: document.getElementById("shopOrigin").value,
                file: jsonValues,
            };
            setLoading(true);
            let response = await axios.post("/api/jsonImportApi", {
                data: payLoad,
            });
            console.log(response);
            if (response.data.status == "success") {
                setToastContent(response.data.message);
                toggleActive();
            } else {
                setToastContent1(response.data.message);
                toggleActive1();
            }
        } catch (err) {
            console.log(err);
        }
        setLoading(false);
    };

    const saveFile = () => {
        saveAs(
            "https://gemfind.net/RingBuilderSampleFiles/product_json.json",
            "product_template.json"
        );
    };
    return (
        <div style={{ height: "200px" }}>
            <Frame>
                <Layout>
                    <Layout.Section>
                        <Card title="Instructions to bulk upload or manual upload your settings">
                            <Card.Section>
                                <Heading element="h3">
                                    For JSON Bulk Upload
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
                                        Please download your sample file{" "}
                                        <a
                                            href="https://gemfind.net/RingBuilderSampleFiles/sampleJsonFile.zip"
                                            download
                                        >
                                            here
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
                                </List>
                            </Card.Section>
                            <Card.Section title="SELECT YOUR JSON FILE TO BULK PRODUCT UPLOAD:">
                                <Card.Subsection>
                                    <form>
                                        <input
                                            type={"file"}
                                            accept={".json"}
                                            onChange={changeJsonHandler}
                                            className="primary"
                                        />
                                    </form>
                                </Card.Subsection>
                            </Card.Section>
                            <Card.Section>
                                <Card.Subsection>
                                    <FormLayout.Group>
                                        <Button
                                            loading={loading}
                                            onClick={() =>
                                                handleJsonImportApi()
                                            }
                                            primary
                                        >
                                            Save
                                        </Button>
                                        {toastMarkup}
                                        {toastMarkup1}
                                    </FormLayout.Group>
                                </Card.Subsection>
                            </Card.Section>
                        </Card>
                    </Layout.Section>
                </Layout>
            </Frame>
        </div>
    );
}

export default JsonImport;
