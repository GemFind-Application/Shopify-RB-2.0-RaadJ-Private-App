import {
    Banner,
    Button,
    Card,
    Checkbox,
    ChoiceList,
    FormLayout,
    Frame,
    Heading,
    Layout,
    List,
    SkeletonBodyText,
    SkeletonDisplayText,
    SkeletonPage,
    TextContainer,
    TextField,
    TextStyle,
    Toast,
} from "@shopify/polaris";
import React, { useCallback, useEffect, useState } from "react";
import Customer from "./Customer";
import ImportFunctions from "./ImportFunctions";
import SettingsForm from "./SettingsForm";

function Settings(props) {
    //SHOW CUSTOMER
    const [showCustomer, setShowCustomer] = useState([]);
    const [showTable, setShowTable] = useState();
    const [importType, setImportType] = useState();

    //CHECK IF CUSTOMER EXISTS

    const handlecallback = (e) => {
        console.log("somin");
        console.log(e);
        props.callback(e);
    };

    useEffect(() => {
        const getCustomer = async () => {
            try {
                const res = await fetch(
                    "/api/ifCustomerExists/" +
                        document.getElementById("shopOrigin").value,
                    {
                        method: "GET",
                    }
                );
                const customer = await res.json();
                // console.log(customer);
                setShowCustomer(customer);
                setShowTable(true);
            } catch (error) {
                console.log(error);
            }
        };
        getCustomer();
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

    // console.log(showCustomer);
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
    if (showCustomer === 0) {
        return <Customer />;
    } else if (importType !== "0") {
        return <SettingsForm />;
    } else {
        return <ImportFunctions callback={handlecallback} />;
    }
}

export default Settings;
