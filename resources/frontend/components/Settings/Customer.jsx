import {
    CalloutCard,
    List,
    Button,
    Card,
    FormLayout,
    Frame,
    TextField,
    Toast,
} from "@shopify/polaris";
import React, { useCallback, useEffect, useState } from "react";
import ImportFunctions from "./ImportFunctions";
import SettingsForm from "./SettingsForm";

function Customer() {
    //SHOW CUSTOMER
    const [showCustomer, setShowCustomer] = useState(0);
    const [showTable, setShowTable] = useState();
    const [importType, setImportType] = useState();

    //CHECK IF CUSTOMER EXISTS

    useEffect(() => {
        const getCustomer = async () => {
            const res = await fetch(
                "/api/ifCustomerExists/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const customer = await res.json();
            setShowCustomer(customer);
            setShowTable(true);
        };
        getCustomer();
    }, []);

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

    const [loading, setLoading] = useState(false);
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

    //CUSTOMER FORM FIELDS
    const [business, setBusiness] = useState("");
    const handleBusiness = useCallback((value) => setBusiness(value), []);
    const [fullname, setFullname] = useState("");
    const handleFullname = useCallback((value) => setFullname(value), []);
    const [address, setAddress] = useState("");
    const handleAddress = useCallback((value) => setAddress(value), []);
    const [state, setState] = useState("");
    const handleState = useCallback((value) => setState(value), []);
    const [city, setCity] = useState("");
    const handleCity = useCallback((value) => setCity(value), []);
    const [zipcode, setZipcode] = useState("");
    const handleZipcode = useCallback((value) => setZipcode(value), []);
    const [telephone, setTelephone] = useState("");
    const handleTelephone = useCallback((value) => setTelephone(value), []);
    const [website, setWebsite] = useState("");
    const handleWebsite = useCallback((value) => setWebsite(value), []);
    const [email, setEmail] = useState("");
    const handleEmail = useCallback((value) => setEmail(value), []);
    const [notes, setNotes] = useState("");
    const handleNotes = useCallback((value) => setNotes(value), []);

    //SAVE CUSTOMER API
    let handleCustomer = async (e) => {
        try {
            let payLoad = {
                shopDomain: document.getElementById("shopOrigin").value,
                business: business,
                fullname: fullname,
                address: address,
                state: state,
                city: city,
                zipcode: zipcode,
                website: website,
                telephone: telephone,
                email: email,
                notes: notes,
            };
            setLoading(true);
            let response = await axios.post("/api/saveCustomer", {
                data: payLoad,
            });
            let customer = 1;
            console.log(response);
            if (response.data.status == "success") {
                setToastContent(response.data.message);
                setShowCustomer(customer);
                toggleActive();
                window.location.reload(false);
            } else {
                setToastContent1(response.data.message);
                toggleActive1();
            }
        } catch (err) {
            console.log(err);
        }
        setLoading(false);
    };

    console.log(showCustomer);

    if (showCustomer === 1) {
        if (importType === "0") {
            return <ImportFunctions />;
        } else {
            return <SettingsForm />;
        }
    } else {
        return (
            <div>
                <Frame>
                    <CalloutCard
                        title="Next Steps for Getting Started with GemFind RingBuilderⓇ
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
                                    Your RingBuilder app requires a Jewelcloud
                                    account with GemFind.
                                </List.Item>
                                <List.Item>
                                    Once your Jewelcloud account has been
                                    activated our support team will email your
                                    Jewelcloud account information and
                                    instructions for selecting your diamond
                                    vendors and setting your markups.
                                </List.Item>
                                <List.Item>
                                    Once you receive your JewelCloud account
                                    details, make sure to replace demo Dealer Id
                                    (1089) with your JewelCloud Dealer ID
                                    account number as well as add your “Admin
                                    Email Address” to receive the notifications.
                                </List.Item>
                            </List>
                        </Card.Section>
                    </CalloutCard>
                    <Card sectioned>
                        <FormLayout.Group condensed>
                            <TextField
                                label="Name of Business"
                                value={business}
                                onChange={handleBusiness}
                                autoComplete="off"
                            />
                            <TextField
                                label="Primary Contact (First & Last Name)"
                                value={fullname}
                                onChange={handleFullname}
                                autoComplete="off"
                            />
                        </FormLayout.Group>
                        <FormLayout.Group condensed>
                            <TextField
                                label="Address"
                                value={address}
                                onChange={handleAddress}
                                autoComplete="off"
                            />
                            <TextField
                                label="State"
                                value={state}
                                onChange={handleState}
                                autoComplete="off"
                            />
                        </FormLayout.Group>
                        <FormLayout.Group condensed>
                            <TextField
                                label="City"
                                value={city}
                                onChange={handleCity}
                                autoComplete="off"
                            />
                            <TextField
                                label="Zipcode"
                                value={zipcode}
                                onChange={handleZipcode}
                                autoComplete="off"
                            />
                        </FormLayout.Group>
                        <FormLayout.Group condensed>
                            <TextField
                                label="Telephone"
                                value={telephone}
                                onChange={handleTelephone}
                                autoComplete="off"
                            />
                            <TextField
                                label="Website Url"
                                value={website}
                                onChange={handleWebsite}
                                autoComplete="off"
                            />
                        </FormLayout.Group>
                        <FormLayout.Group>
                            <TextField
                                label="Email Address"
                                value={email}
                                onChange={handleEmail}
                                autoComplete="off"
                            />
                            <TextField
                                label="Notes"
                                value={notes}
                                onChange={handleNotes}
                                multiline={4}
                                autoComplete="off"
                            />
                        </FormLayout.Group>
                        <FormLayout.Group>
                            <Button
                                loading={loading}
                                onClick={() => handleCustomer()}
                                primary
                            >
                                Save
                            </Button>
                            {toastMarkup}
                            {toastMarkup1}
                        </FormLayout.Group>
                    </Card>
                </Frame>
            </div>
        );
    }
}

export default Customer;
