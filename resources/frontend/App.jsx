import { BrowserRouter } from "react-router-dom";
import { NavigationMenu } from "@shopify/app-bridge-react";
import { Tabs, Tab, AppBar } from "@material-ui/core";
import Routes from "./Routes";
import {
    AppBridgeProvider,
    QueryProvider,
    PolarisProvider,
} from "./components/providers";
import { useEffect, useState } from "react";
import {
    Frame,
    Layout,
    SkeletonBodyText,
    SkeletonDisplayText,
    SkeletonPage,
    Stack,
    Button,
    Card,
    Page,
    TextContainer,
} from "@shopify/polaris";
import Knowledge from "../frontend/components/Knowledge/Knowledge";
import Settings from "../frontend/components/Settings/Settings";
import Plan from "../frontend/components/Plan/Plan";
import Import from "../frontend/components/Import/Import";

import CSSConfiguration from "../frontend/components/CSS Configuration/CSSConfiguration";

export default function App() {
    // Any .tsx or .jsx files in /pages will become a route
    // See documentation for <Routes /> for more info
    const pages = import.meta.globEager(
        "./pages/**/!(*.test.[jt]sx)*.([jt]sx)"
    );
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
            setSelectedTab(0);
        }
        setShowTable(true);
    };

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

    //GET SETTINGS API

    useEffect(() => {
        getSettingsData();
        getPlanId();
    }, []);

    if (showTable === false) {
        return (
            <div>
                <PolarisProvider>
                    <BrowserRouter>
                        <AppBridgeProvider>
                            <QueryProvider>
                                <Frame>
                                    <Card>
                                        <SkeletonPage primaryAction>
                                            <Layout>
                                                <Layout.Section>
                                                    <Card sectioned>
                                                        <SkeletonBodyText />
                                                    </Card>
                                                    <Card sectioned>
                                                        <Stack>
                                                            <SkeletonDisplayText size="small" />
                                                            <SkeletonBodyText />
                                                        </Stack>
                                                    </Card>
                                                </Layout.Section>
                                            </Layout>
                                        </SkeletonPage>
                                    </Card>
                                </Frame>
                            </QueryProvider>
                        </AppBridgeProvider>
                    </BrowserRouter>
                </PolarisProvider>
            </div>
        );
    }
    // else if (planExists === "0") {
    //     console.log("my plan tab");
    //     return (
    //         <>
    //             <PolarisProvider>
    //                 <BrowserRouter>
    //                     <AppBridgeProvider>
    //                         <QueryProvider>
    //                             <Page fullWidth>
    //                                 <Tabs
    //                                     value={selectedTab}
    //                                     onChange={handleChange}
    //                                 >
    //                                     <Tab
    //                                         style={{ fontSize: "12px" }}
    //                                         label="My Plan"
    //                                     />
    //                                 </Tabs>
    //                                 {selectedTab === 0 && (
    //                                     <Plan callback={handletab} />
    //                                 )}
    //                             </Page>
    //                         </QueryProvider>
    //                     </AppBridgeProvider>
    //                 </BrowserRouter>
    //             </PolarisProvider>
    //         </>
    //     );
    //}
    else if (showCustomer === 0) {
        console.log("not customer");
        return (
            <>
                <PolarisProvider>
                    <BrowserRouter>
                        <AppBridgeProvider>
                            <QueryProvider>
                                <Page fullWidth>
                                    <Tabs
                                        value={selectedTab}
                                        onChange={handleChange}
                                    >
                                        <Tab
                                            style={{ fontSize: "12px" }}
                                            label="Settings"
                                        />
                                        {/* <Tab style={{ fontSize: "12px" }} label="My Plans" /> */}
                                        <Tab
                                            style={{ fontSize: "12px" }}
                                            label="Knowledge Base"
                                        />
                                    </Tabs>
                                    {selectedTab === 0 && (
                                        <Settings callback={handletab} />
                                    )}
                                    {/* {selectedTab === 1 && <Plan />} */}
                                    {selectedTab === 1 && <Knowledge />}
                                </Page>
                            </QueryProvider>
                        </AppBridgeProvider>
                    </BrowserRouter>
                </PolarisProvider>
            </>
        );
    } else if (importType === "2") {
        console.log("else 3..!");
        return (
            <>
                <PolarisProvider>
                    <BrowserRouter>
                        <AppBridgeProvider>
                            <QueryProvider>
                                <Page fullWidth>
                                    <Tabs
                                        value={selectedTab}
                                        onChange={handleChange}
                                    >
                                        <Tab
                                            style={{ fontSize: "12px" }}
                                            label="Settings"
                                        />
                                        {/* <Tab style={{ fontSize: "12px" }} label="My Plans" /> */}
                                        {/* <Tab
                                            style={{ fontSize: "12px" }}
                                            label="CSV Import"
                                        /> */}
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
                                    {selectedTab === 0 && (
                                        <Settings callback={handletab} />
                                    )}
                                    {/* {selectedTab === 1 && <Plan />} */}
                                    {/* {selectedTab === 1 && <Import />} */}
                                    {selectedTab === 1 && <Knowledge />}
                                    {selectedTab === 2 && <CSSConfiguration />}
                                </Page>
                            </QueryProvider>
                        </AppBridgeProvider>
                    </BrowserRouter>
                </PolarisProvider>
            </>
        );
    } else {
        console.log("els me gaya");
        return (
            <>
                <PolarisProvider>
                    <BrowserRouter>
                        <AppBridgeProvider>
                            <QueryProvider>
                                <Page fullWidth>
                                    <Tabs
                                        value={selectedTab}
                                        onChange={handleChange}
                                    >
                                        <Tab
                                            style={{ fontSize: "12px" }}
                                            label="Settings"
                                        />
                                        <Tab
                                            style={{ fontSize: "12px" }}
                                            label="Knowledge Base"
                                        />
                                        <Tab
                                            style={{ fontSize: "12px" }}
                                            label="CSS Configuration"
                                        />
                                    </Tabs>
                                    {selectedTab === 0 && (
                                        <Settings callback={handletab} />
                                    )}
                                    {selectedTab === 1 && <Knowledge />}
                                    {selectedTab === 2 && <CSSConfiguration />}
                                </Page>
                            </QueryProvider>
                        </AppBridgeProvider>
                    </BrowserRouter>
                </PolarisProvider>
            </>
        );
    }
}
