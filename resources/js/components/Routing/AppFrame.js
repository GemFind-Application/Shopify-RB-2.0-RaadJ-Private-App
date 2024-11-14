import React, { useEffect, useState } from "react";
import { Tabs, Tab, AppBar } from "@material-ui/core";
import GettingStarted from "../Home/GettingStarted";
import Knowledge from "../Knowledge/Knowledge";
import Settings from "../Settings/Settings";
import Plan from "../Plan/Plan";
import {
    Button,
    Card,
    Frame,
    Layout,
    Page,
    SkeletonBodyText,
    SkeletonDisplayText,
    SkeletonPage,
    TextContainer,
} from "@shopify/polaris";
import Smtp from "../SMTP/Smtp";
import Import from "../Import/Import";
import CSSConfiguration from "../CSS Configuration/CSSConfiguration";

const AppFrame = (props) => {
    const [selectedTab, setSelectedTab] = useState(0);
    const [planExists, setPlanExists] = useState("");
    const [showCustomer, setShowCustomer] = useState();
    const [importType, setImportType] = useState();

    const handleChange = (event, newValue) => {
        setSelectedTab(newValue);
    };

    const handletab = (event) => {
        setSelectedTab(event);
    };
    const [showTable, setShowTable] = useState(false);

    const getCustomer = async () => {
        // console.log("checking customer");
        const res = await fetch(
            "/api/ifCustomerExists/" +
                document.getElementById("shopOrigin").value,
            {
                method: "GET",
            }
        );
        const customer = await res.json();
        setShowCustomer(customer);
    };

    //GET SETTINGS API
    useEffect(() => {}, []);

    useEffect(() => {
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
            if (settingProduct.type_1 === "2") {
                setSelectedTab(1);
            }
            setShowTable(true);
        };
        getSettingsData();

        const getPlanId = async () => {
            const res = await fetch(
                "/api/ifPlanIdExists/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const plan = await res.json();
            // console.log(plan);
            setPlanExists(plan.data.planIdExists.plan);
            if (plan.data.planIdExists.plan === "1") {
                getCustomer();
            }
            setShowTable(true);
        };
        getPlanId();
    }, []);

    // console.log("checking props val");
    console.log(props);
    if (showTable === false) {
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
    // else if (planExists === "0") {
    //     console.log("else 1");
    //     return (
    //         <>
    //             <Page fullWidth>
    //                 <Tabs value={selectedTab} onChange={handleChange}>
    //                     <Tab style={{ fontSize: "12px" }} label="My Plans" />
    //                     <Tab
    //                         style={{ fontSize: "12px" }}
    //                         label="Knowledge Base"
    //                     />
    //                 </Tabs>
    //                 {selectedTab === 0 && <Plan />}
    //                 {selectedTab === 1 && <Knowledge />}
    //             </Page>
    //         </>
    //     );
    // }
    else if (showCustomer === 0) {
        console.log("not customer");
        return (
            <>
                <Page fullWidth>
                    <Tabs value={selectedTab} onChange={handleChange}>
                        <Tab style={{ fontSize: "12px" }} label="Settings" />
                        {/* <Tab style={{ fontSize: "12px" }} label="My Plans" /> */}
                        <Tab
                            style={{ fontSize: "12px" }}
                            label="Knowledge Base"
                        />
                    </Tabs>
                    {selectedTab === 0 && <Settings callback={handletab} />}
                    {/* {selectedTab === 1 && <Plan />} */}
                    {selectedTab === 1 && <Knowledge />}
                </Page>
            </>
        );
    } else if (importType === "2") {
        console.log("else 3");
        return (
            <>
                <Page fullWidth>
                    <Tabs value={selectedTab} onChange={handleChange}>
                        <Tab style={{ fontSize: "12px" }} label="Settings" />
                        {/* <Tab style={{ fontSize: "12px" }} label="My Plans" /> */}
                        <Tab style={{ fontSize: "12px" }} label="CSV Import" />
                        <Tab
                            style={{ fontSize: "12px" }}
                            label="Knowledge Base"
                        />
                        <Tab
                            style={{ fontSize: "12px" }}
                            label="CSS Configuration"
                        />
                    </Tabs>
                    {/* </AppBar> */}
                    {selectedTab === 0 && <Settings callback={handletab} />}
                    {/* {selectedTab === 1 && <Plan />} */}
                    {selectedTab === 1 && <Import />}
                    {selectedTab === 2 && <Knowledge />}
                    {selectedTab === 3 && <CSSConfiguration />}
                </Page>
            </>
        );
    } else {
        console.log("els me gaya");
        return (
            <>
                <Page fullWidth>
                    <Tabs value={selectedTab} onChange={handleChange}>
                        <Tab style={{ fontSize: "12px" }} label="Settings" />
                        {/* <Tab style={{ fontSize: "12px" }} label="My Plans" /> */}
                        <Tab
                            style={{ fontSize: "12px" }}
                            label="Knowledge Base"
                        />
                        <Tab
                            style={{ fontSize: "12px" }}
                            label="CSS Configuration"
                        />
                    </Tabs>
                    {/* </AppBar> */}
                    {selectedTab === 0 && <Settings callback={handletab} />}
                    {/* {selectedTab === 1 && <Plan />} */}
                    {selectedTab === 1 && <Knowledge />}
                    {selectedTab === 2 && <CSSConfiguration />}
                </Page>
            </>
        );
    }
};

export default AppFrame;
