import {
    Button,
    Card,
    FormLayout,
    Frame,
    Select,
    TextContainer,
    SkeletonPage,
    SkeletonBodyText,
    TextField,
    Toast,
    SkeletonDisplayText,
    Layout,
} from "@shopify/polaris";
import axios from "axios";
import React, { useCallback, useEffect, useState } from "react";

function Smtp() {
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

    //for encryption
    const [selected, setSelected] = useState("");
    const handleSelectChange = useCallback((value) => setSelected(value), []);
    const options = [
        { label: "None", value: "none" },
        { label: "SSL", value: "ssl" },
        { label: "TLS", value: "tls" },
    ];

    const [host, setHost] = useState("");
    const handleHost = useCallback((value) => setHost(value), []);
    const [port, setPort] = useState("");
    const handlePort = useCallback((value) => setPort(value), []);
    const [username, setUsername] = useState("");
    const handleName = useCallback((value) => setUsername(value), []);
    const [password, setPassword] = useState("");
    const handlePassword = useCallback((value) => setPassword(value), []);

    const getSmtpData = async () => {
        try {
            const res = await fetch(
                "/api/getSmtpConfiguration/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const settingProduct = await res.json();
            console.log(settingProduct);
            setSelected(settingProduct.protocol);
            setHost(settingProduct.smtphost);
            setPort(settingProduct.port);
            setUsername(settingProduct.smtpusername);
            setPassword(settingProduct.smtppassword);
            setShowTable(true);
        } catch (error) {
            console.log(error);
        }
    };

    useEffect(() => {
        getSmtpData();
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
    }
    if (showTable) {
        return (
            <div>
                <Frame>
                    <Card
                        sectioned
                        title="If you want to make this app send the emails from your own email address then you need to fill the details of the form and save"
                    >
                        <FormLayout>
                            <Select
                                label="ENCRYPTION"
                                options={options}
                                onChange={handleSelectChange}
                                value={selected}
                            />
                            <TextField
                                label="SMTP HOST"
                                value={host}
                                onChange={handleHost}
                                autoComplete="off"
                            />
                            <TextField
                                label="SMTP PORT"
                                value={port}
                                onChange={handlePort}
                                autoComplete="off"
                            />
                            <TextField
                                label="SMTP USERNAME"
                                value={username}
                                onChange={handleName}
                                autoComplete="off"
                            />
                            <TextField
                                label="SMTP PASSWORD"
                                type="password"
                                value={password}
                                onChange={handlePassword}
                                autoComplete="off"
                            />
                            <Button
                                loading={loading}
                                onClick={() => handleSubmit()}
                                primary
                            >
                                Save
                            </Button>
                            {toastMarkup}
                            {toastMarkup1}
                        </FormLayout>
                    </Card>
                </Frame>
            </div>
        );
    }
}

export default Smtp;
