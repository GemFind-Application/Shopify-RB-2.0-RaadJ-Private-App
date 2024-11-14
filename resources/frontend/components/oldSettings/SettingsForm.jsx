import { ClassNames } from "@emotion/react";
import {
    Banner,
    CalloutCard,
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
    Toast,
    VisuallyHidden,
    AccountConnection,
} from "@shopify/polaris";
import React, { useCallback, useEffect, useState } from "react";
import ImportFunctions from "./ImportFunctions";

function SettingsForm(props) {
    const [importType, setImportType] = useState();
    const [planTryOn, setPlanTryOn] = useState();
    const [csvImport, setCsvImport] = useState();
    const [loading, setLoading] = useState(false);
    const [showTable, setShowTable] = useState();
    const [currentVersion, setCurrentVersion] = useState("");
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

    //SETTINGS FORM
    const [dealerId, setDealerId] = useState("");
    const handleDealerId = useCallback((value) => setDealerId(value), []);
    const [adminEmail, setAdminEmail] = useState("");
    const handleAdminEmail = useCallback((value) => setAdminEmail(value), []);
    const [enableHint, setEnableHint] = useState([]);
    const handleEnableHint = useCallback((value) => setEnableHint(value), []);
    const [enableEmail, setEnableEmail] = useState([]);
    const handleEnableEmail = useCallback((value) => setEnableEmail(value), []);
    const [enableSchedule, setEnableSchedule] = useState([]);
    const handleEnableSchedule = useCallback(
        (value) => setEnableSchedule(value),
        []
    );
    const [enableInfo, setEnableInfo] = useState([]);
    const handleEnableInfo = useCallback((value) => setEnableInfo(value), []);
    const [enablePrint, setEnablePrint] = useState([]);
    const handleEnablePrint = useCallback((value) => setEnablePrint(value), []);
    const [enableNotification, setEnableNotification] = useState([]);
    const handleEnableNotification = useCallback(
        (value) => setEnableNotification(value),
        []
    );
    const [showInfo, setShowInfo] = useState([]);
    const handleShowInfo = useCallback((value) => setShowInfo(value), []);
    const [diamondListing, setDiamondListing] = useState([]);
    const handleDiamondListing = useCallback(
        (value) => setDiamondListing(value),
        []
    );
    const [showPowered, setShowPowered] = useState([]);
    const handleShowPowered = useCallback((value) => setShowPowered(value), []);
    const [enableSticky, setEnableSticky] = useState([]);
    const [priceLocation, setCurrencyLocation] = useState([]);
    const [defaultDiamondImage, setDefaultDiamondImage] = useState([]);

    const handleEnableSticky = useCallback(
        (value) => setEnableSticky(value),
        []
    );
    const handleCurrencyLocation = useCallback(
        (value) => setCurrencyLocation(value),
        []
    );
    const handleDiamondDefaultImage = useCallback(
        (value) => setDefaultDiamondImage(value),
        []
    );
    const [shop, setShop] = useState("");
    const handleShop = useCallback((value) => setShop(value), []);
    const [shopLogo, setShopLogo] = useState("");
    const handleShopLogo = useCallback((value) => setShopLogo(value), []);
    const [announcementText, setAnnouncementText] = useState("");
    const handleAnnouncementText = useCallback(
        (value) => setAnnouncementText(value),
        []
    );
    const [announceRbDetail, setAnnounceRbDetail] = useState("");
    const handleAnnounceRbDetail = useCallback(
        (value) => setAnnounceRbDetail(value),
        []
    );
    const [diamondMetaTitle, setDiamondMetaTitle] = useState("");
    const handleDiamondMetaTitle = useCallback(
        (value) => setDiamondMetaTitle(value),
        []
    );
    const [diamondMetaDescription, setDiamondMetaDescription] = useState("");
    const handleDiamondMetaDescription = useCallback(
        (value) => setDiamondMetaDescription(value),
        []
    );
    const [checked, setChecked] = useState(false);
    const handleChangeTryon = useCallback(
        (newChecked) => setChecked(newChecked),
        []
    );
    const [valueCarat, setValue] = useState();
    const handleChangeCarat = useCallback((newValue) => setValue(newValue), []);

    const [siteKey, setSiteKey] = useState("");
    const handleSiteKey = useCallback(
        (value) => setSiteKey(value),
        []
    );

    const [secretKey, setSecretKey] = useState("");
    const handlesecretKey = useCallback(
        (value) => setSecretKey(value),
        []
    );

    //GET SETTINGS API
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
            setDealerId(settingProduct.dealerid);
            setAdminEmail(settingProduct.admin_email_address);
            setEnableHint(settingProduct.enable_hint);
            setEnableEmail(settingProduct.enable_email_friend);
            setEnableSchedule(settingProduct.enable_schedule_viewing);
            setEnableInfo(settingProduct.enable_more_info);
            setEnablePrint(settingProduct.enable_print);
            setEnableNotification(settingProduct.enable_admin_notification);
            setShowInfo(settingProduct.show_filter_info);
            setDiamondListing(settingProduct.default_viewmode);
            setShowPowered(settingProduct.show_powered_by);
            setEnableSticky(settingProduct.enable_sticky_header);
            setShop(settingProduct.shop);
            setAnnouncementText(settingProduct.announcement_text);
            setAnnounceRbDetail(settingProduct.announcement_text_rbdetail);
            setShopLogo(settingProduct.shop_logo);
            setValue(settingProduct.settings_carat_ranges);
            setChecked(settingProduct.display_tryon);
            setImportType(settingProduct.type_1);
            setCurrencyLocation(settingProduct.price_row_format);
            setDefaultDiamondImage(settingProduct.showDefaultDiamondImage);
            setDiamondMetaTitle(settingProduct.diamond_meta_title);
            setDiamondMetaDescription(settingProduct.diamond_meta_description);
            setSiteKey(settingProduct.site_key);
            setSecretKey(settingProduct.secret_key);
            setShowTable(true);
           
            
        };
        getSettingsData();
        //VERSION API
        const getCurrentVersion = async () => {
            const res = await fetch("/api/getCurrentVersion", {
                method: "GET",
            });
            const version = await res.json();
            setCurrentVersion(version.data);
        };
        getCurrentVersion();
        //PLAN
        const getPlanId = async () => {
            const res = await fetch(
                "/api/ifPlanIdExists/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const plan = await res.json();
            setPlanTryOn(plan.data.charges.try_button);
        };
        getPlanId();
    }, []);

    //FOR VIEW FRONTEND
    const [import_type, setImport_type] = useState("");
    const [getViewCard, setGetViewCard] = useState(false);
    // const [notGetViewCard, setNotGetViewCard] = useState("inactive");
    const handleImport_type = useCallback((value) => setImport_type(value), []);
    useEffect(() => {
        const getImportType = async () => {
            const res = await fetch(
                "/api/getSettingsData/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const importOption = await res.json();
            var shop_domain = document.getElementById("shopOrigin").value;

            var api_url = "https://" + shop_domain + "/apps/diamondtools";
            // console.log(shop_domain);
            // console.log(importOption.type_1);
            if (importOption.type_1 == "1") {
                setGetViewCard(true);
            }
            // if (importOption.type_1 == "1") {
            // console.log("api");
            setImport_type(api_url);
            // }
        };
        getImportType();
    }, []);

    //SAVE SETTINGS API
    let handleSettings = async (e) => {
        try {
            let payLoad = {
                shopDomain: document.getElementById("shopOrigin").value,
                dealerId: dealerId,
                adminEmail: adminEmail,
                enableHint: enableHint,
                enableEmail: enableEmail,
                enableSchedule: enableSchedule,
                enableInfo: enableInfo,
                enablePrint: enablePrint,
                enableNotification: enableNotification,
                showInfo: showInfo,
                diamondListing: diamondListing,
                showPowered: showPowered,
                enableSticky: enableSticky,
                valueCarat: valueCarat,
                announcementText: announcementText,
                announceRbDetail: announceRbDetail,
                checked: checked,
                shopLogo: shopLogo,
                priceLocation: priceLocation,
                defaultDiamondImage : defaultDiamondImage,
                diamondMetaTitle: diamondMetaTitle,
                diamondMetaDescription: diamondMetaDescription,
                siteKey: siteKey,
                secretKey: secretKey,

            };
            setLoading(true);
            let response = await axios.post("/api/saveSettings", {
                data: payLoad,
            });
            // console.log(response.data);
            if (response.data.status == true) {
                // if (csvImport === "2") {
                //     console.log("hrllo");
                //     // g("hi");
                //     props.callback(2);
                // }
                setToastContent(response.data.message);
                toggleActive();
                location.reload();
            } else {
                setToastContent1(response.data.message);
                toggleActive1();
            }
        } catch (err) {
            console.log(err);
        }
        setLoading(false);
    };
    // const handleAction = useCallback(() => {
    //     setConnected((connected) => !connected);
    // }, [connected]);

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
        // } else if (importType === "0") {
        //     return <ImportFunctions />;
    } else {
        return (
            <div>
                <Frame>
                    <div
                        className="view-button"
                        style={{
                            marginBottom: "15px",

                            // display: `${
                            //     getViewCard === true ? "block" : "none"
                            // } `,
                        }}
                    >
                        <Card title="To view your store in frontend please click on 'View In Frontend' button.">
                            <a
                                href={`${import_type}`}
                                className="primary"
                                target="_blank"
                                style={{
                                    padding: "10px",
                                    backgroundColor: "#198754",
                                    color: "#fff",
                                    textDecoration: "none",
                                    position: "relative",
                                    float: "right",

                                    top: "-12px",
                                    right: "20px",
                                }}
                            >
                                View In Frontend
                            </a>
                            <p
                                style={{
                                    color: "green",
                                    fontWeight: "bold",
                                    padding: "20px",
                                }}
                            >
                                Current Version : {currentVersion}
                            </p>
                        </Card>
                    </div>
                    <CalloutCard
                        title="Next Steps for Getting Started with GemFind DiamondLinkⓇ
            "
                        illustration="https://gemfind.net/RingBuilderSampleFiles/callout-card.svg"
                        primaryAction={{
                            content:
                                "Got a question? Contact us at support@gemfind.com or 1-949-752-7710.",
                            url: "#",
                        }}
                    >
                        {/* <div style={{ color: "#bf0711" }}>
                        <Button primary>View In Frontend</Button>
                    </div> */}
                        <Card.Section>
                            <List>
                                <List.Item>
                                Thank you for installing The DiamondLinkⓇ application powered by GemFind. You will receive an email from our Support Team shortly.
                                </List.Item>
                                <List.Item>
                                Please provide collaborative access of the store to GemFind Support Team once you received the email from Shopify.
                                </List.Item>
                              
                            </List>
                        </Card.Section>
                    </CalloutCard>
                    <Card sectioned>
                        <FormLayout.Group condensed>
                            <TextField
                                label="JewelCloud Account ID"
                                value={dealerId}
                                onChange={handleDealerId}
                                autoComplete="off"
                            />
                            <TextField
                                type="email"
                                label="Admin Email Address"
                                value={adminEmail}
                                onChange={handleAdminEmail}
                                autoComplete="email"
                            />
                        </FormLayout.Group>
                        <FormLayout.Group condensed>
                            <ChoiceList
                                title="Enable A Hint"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    {
                                        label: "No",
                                        value: "0",
                                    },
                                ]}
                                selected={enableHint}
                                onChange={handleEnableHint}
                                className="Polaris-ChoiceList--horizontal"
                            />
                            <ChoiceList
                                title="Enable Email A Friend"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    {
                                        label: "No",
                                        value: "0",
                                    },
                                ]}
                                selected={enableEmail}
                                onChange={handleEnableEmail}
                            />
                            <ChoiceList
                                title="Enable Schedule A Viewing"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    {
                                        label: "No",
                                        value: "0",
                                    },
                                ]}
                                selected={enableSchedule}
                                onChange={handleEnableSchedule}
                            />
                            <ChoiceList
                                title="Enable Request More Info"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    {
                                        label: "No",
                                        value: "0",
                                    },
                                ]}
                                selected={enableInfo}
                                onChange={handleEnableInfo}
                            />
                            <ChoiceList
                                title="Enable Print"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    {
                                        label: "No",
                                        value: "0",
                                    },
                                ]}
                                selected={enablePrint}
                                onChange={handleEnablePrint}
                            />
                        </FormLayout.Group>
                        <FormLayout.Group condensed>
                            <ChoiceList
                                title="Enable Admin Notification"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    {
                                        label: "No",
                                        value: "0",
                                    },
                                ]}
                                selected={enableNotification}
                                onChange={handleEnableNotification}
                            />
                            <ChoiceList
                                title="Show Info Box?"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    {
                                        label: "No",
                                        value: "0",
                                    },
                                ]}
                                selected={showInfo}
                                onChange={handleShowInfo}
                            />
                            <ChoiceList
                                title="Diamond Listing Default View"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    {
                                        label: "No",
                                        value: "0",
                                    },
                                ]}
                                selected={diamondListing}
                                onChange={handleDiamondListing}
                            />
                            <ChoiceList
                                title="Show Powered By GemFind?"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    {
                                        label: "No",
                                        value: "0",
                                    },
                                ]}
                                selected={showPowered}
                                onChange={handleShowPowered}
                            />
                            {/* <ChoiceList
                                title="Enable Sticky Header?"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    {
                                        label: "No",
                                        value: "0",
                                    },
                                ]}
                                selected={enableSticky}
                                onChange={handleEnableSticky}
                            /> */}
                        </FormLayout.Group>
                        <FormLayout.Group condensed>
                            <ChoiceList
                                title="Currency Symbol Position"
                                choices={[
                                    { label: "Right", value: "1" },
                                    { label: "Left", value: "0" },
                                ]}
                                selected={priceLocation}
                                onChange={handleCurrencyLocation}
                            />
                             <ChoiceList
                                title="Display Real Diamond Image by Default"
                                choices={[
                                    { label: "Yes", value: "1" },
                                    { label: "No", value: "0" },
                                ]}
                                selected={defaultDiamondImage}
                                onChange={handleDiamondDefaultImage}
                            />
                        </FormLayout.Group>
                       
                        {planTryOn === "ACTIVE" ? (
                            <FormLayout.Group>
                                <TextField
                                    label="Shop"
                                    value={shop}
                                    disabled
                                    autoComplete="off"
                                />
                                <Checkbox
                                    label="Display Tryon Button"
                                    checked={checked}
                                    onChange={handleChangeTryon}
                                />
                            </FormLayout.Group>
                        ) : null}
                        <FormLayout.Group>
                            <TextField
                                label="Top TextArea"
                                value={announcementText}
                                onChange={handleAnnouncementText}
                                multiline={2}
                                autoComplete="off"
                            />
                            <TextField
                                label="Diamond Details TextArea"
                                value={announceRbDetail}
                                onChange={handleAnnounceRbDetail}
                                multiline={2}
                                autoComplete="off"
                            />
                        </FormLayout.Group>
                        <FormLayout.Group>
                            <TextField
                                label="Diamond Meta Title"
                                value={diamondMetaTitle}
                                onChange={handleDiamondMetaTitle}
                                multiline={2}
                                autoComplete="off"
                            />
                            <TextField
                                label="Diamond Meta Description"
                                value={diamondMetaDescription}
                                onChange={handleDiamondMetaDescription}
                                multiline={2}
                                autoComplete="off"
                            />
                        </FormLayout.Group>
                        <FormLayout.Group>
                            <VisuallyHidden>
                                <TextField
                                    hiddenLabel
                                    label="Settings Carat Ranges"
                                    value={valueCarat}
                                    onChange={handleChangeCarat}
                                    multiline={2}
                                    autoComplete="off"
                                />
                            </VisuallyHidden>
                            <VisuallyHidden>
                                <TextField
                                    hiddenLabel
                                    label="Shop Logo URL"
                                    value={shop}
                                    onChange={shop}
                                    autoComplete="off"
                                />
                            </VisuallyHidden>
                        </FormLayout.Group>
                        <FormLayout.Group>
                            <TextField
                                label="Google reCaptcha Site Key"
                                value={siteKey}
                                onChange={handleSiteKey}
                                multiline={2}
                                autoComplete="off"
                            />
                            <TextField
                                label="Google reCaptcha Secret Key"
                                value={secretKey}
                                onChange={handlesecretKey}
                                multiline={2}
                                autoComplete="off"
                            />
                        </FormLayout.Group>
                        <FormLayout.Group>
                            <Button
                                loading={loading}
                                onClick={() => handleSettings()}
                                primary
                            >
                                Submit
                            </Button>
                            {toastMarkup}
                            {toastMarkup1}
                        </FormLayout.Group>
                        <FormLayout.Group>
                            <Banner
                                title="Need help? Below are the description of each field using in configuration."
                                status="info"
                            >
                                <List>
                                    <List.Item>
                                        <Heading> JewelCloud Acount ID</Heading>
                                        <p>
                                            This is mandatory field. GemFind Support team will help you to get the JewelCloud Account ID.                                         
                                        </p>
                                    </List.Item>
                                    <p></p>
                                    <List.Item>
                                        <Heading>Admin Email Address</Heading>
                                        <p>
                                            This is mandatory field. For Emailing purposes, application using this Email Address.                                      
                                        </p>
                                    </List.Item>
                                    <List.Item>
                                        <Heading>Enable A Hint</Heading>
                                        <p>
                                            Toggle to display "Drop A Hint Email" at frontend.
                                        </p>
                                    </List.Item>
                                    <List.Item>
                                        <Heading>Enable Email A Friend</Heading>
                                        <p>
                                            Toggle to display "Email A Friend" at frontend.
                                        </p>
                                    </List.Item>
                                    <List.Item>
                                        <Heading>
                                            Enable Schedule A Viewing
                                        </Heading>
                                        <p>
                                            Toggle to display "Schedule A Viewing" at frontend.
                                        </p>
                                    </List.Item>
                                    <List.Item>
                                        <Heading>Enable Request More Info</Heading>
                                        <p>
                                            Toggle to display "Enable Request More Info" at frontend.
                                        </p>
                                    </List.Item>
                                    <List.Item>
                                        <Heading>Enable Print</Heading>
                                        <p>
                                            Toggle to display "Print" at frontend.
                                        </p>
                                    </List.Item>
                                    <List.Item>
                                        <Heading>
                                            Enable Admin Notification
                                        </Heading>
                                        <p>
                                            Toggle to receive the notification on Admin Email Address.                                         
                                        </p>
                                    </List.Item>
                                    <List.Item>
                                        <Heading>Show Info Box?</Heading>
                                        <p>
                                            Toggle to display "Help Text" at frontend.                                          
                                        </p>
                                    </List.Item>
                                    <List.Item>
                                        <Heading>Show Powered By GemFind?</Heading>
                                        <p>
                                            This will toggle the "Powered By
                                            GemFind" at the footer of the tool.
                                        </p>
                                    </List.Item>
                                    {/* <List.Item>
                                        <Heading>Enable Sticky Header?</Heading>
                                        <p>
                                            This will toggle the sticky table
                                            header at diamond listing page.
                                        </p>
                                    </List.Item> */}
                                    <List.Item>
                                        <Heading>
                                            Diamond Listing Default View
                                        </Heading>
                                        <p>
                                            Decide to display "Dimaond Listing" or "Grid View". Bydefault it is List View.
                                        </p>
                                    </List.Item>
                                    {/* <List.Item>
                                        <Heading>Shop</Heading>
                                        <p>
                                            It is just information purpose that
                                            what is the current store URL we are
                                            using inside configuration
                                        </p>
                                    </List.Item> */}
                                    {/* <List.Item>
                                        <Heading>Settings Carat Ranges</Heading>
                                        <p>
                                            The field is used for setting's
                                            carat ranges
                                        </p>
                                    </List.Item> */}
                                    {/* <List.Item>
                                        <Heading>Shop Logo URL</Heading>
                                        <p>
                                            It will need to enter the current
                                            shop logo URL which will used in
                                            emails which will send to end-users
                                            and store admin.
                                        </p>
                                    </List.Item> */}
                                </List>
                            </Banner>
                        </FormLayout.Group>
                        <FormLayout.Group>
                            <Banner
                                title="Note: If you want to display meta title and meta description for this tool than you should add this code in theme.liquid file before the tags."
                                status="info"
                            >
                                <Heading>For Example</Heading>
                                <List>
                                    <List.Item>
                                        <Heading>For Title</Heading>
                                        <p>
                                            {
                                                "{% unless template == blank %} <title>{{ seo_title | strip }}</title> {% endunless %}"
                                            }
                                        </p>
                                    </List.Item>
                                    <List.Item>
                                        <Heading>For Description</Heading>
                                        <p>
                                            {
                                                '{% unless template == blank %} <meta name="description" content="{{ page_description | escape }}"> {% endunless %}'
                                            }
                                        </p>
                                    </List.Item>
                                </List>
                            </Banner>
                        </FormLayout.Group>
                    </Card>
                </Frame>
            </div>
        );
    }
}

export default SettingsForm;
