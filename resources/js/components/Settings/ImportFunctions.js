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
    const [value, setValue] = useState("1");
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
                value: value,
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
            setImportType(settingProduct.type_1);
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
        if (importType === "0") {
            return (
                <div>
                    <Frame>
                        <CalloutCard
                            title="Next Steps for Getting Started with GemFind RingBuilder
                    "
                            illustration="https://cdn.shopify.com/s/assets/admin/checkout/settings-customizecart-705f57c725ac05be5a34ec20c05b94298cb8afd10aac7bd9c7ad02030f48cfa0.svg"
                            primaryAction={{
                                content:
                                    "Got questions? Contact us at support@gemfind.com or 800-373-4373",
                                url: "#",
                            }}
                        >
                            <Card.Section>
                                <List>
                                    <List.Item>
                                        Your RingBuilder app requires a
                                        Jewelcloud account with GemFind.
                                    </List.Item>
                                    <List.Item>
                                        Once your Jewelcloud account has been
                                        activated our support team will email
                                        your Jewelcloud account information and
                                        instructions for selecting your diamond
                                        vendors and setting your markups.
                                    </List.Item>
                                    <List.Item>
                                        Once you receive your JewelCloud account
                                        details, make sure to replace demo
                                        Dealer Id (1089) with your JewelCloud
                                        Dealer ID account number as well as add
                                        your “Admin Email Address” to receive
                                        the notifications.
                                    </List.Item>
                                </List>
                            </Card.Section>
                        </CalloutCard>
                        <Card>
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
        } else {
            return <SettingsForm />;
        }
    }
}

export default ImportFunctions;
