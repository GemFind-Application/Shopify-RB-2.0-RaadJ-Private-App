import {
    Card,
    Banner,
    TextField,
    DataTable,
    Button,
    Tag,
    Stack,
    InlineError,
    Icon,
} from "@shopify/polaris";
import { DiscountCodeMajor } from "@shopify/polaris-icons";
import React, { useEffect, useState, useCallback } from "react";
import "bootstrap/dist/css/bootstrap.min.css";
function Plan() {
    console.log("process.env.APP_TOTAL_CHARGE");
    console.log(import.meta.env.VITE_APP_TOTAL_CHARGE);
    let shopDomain = document.getElementById("shopOrigin").value;
    const [basicUrl, setBasicUrl] = useState([]);
    const [tryOnUrl, setTryOnUrl] = useState([]);
    const [basicButton, setBasicButton] = useState([]);
    const [tryOnButton, setTryOnButton] = useState([]);
    const [discount, setDiscount] = useState("0");
    const [saving, setSaving] = useState("0");
    const [total, setTotal] = useState(import.meta.env.VITE_APP_TOTAL_CHARGE);
    const [show, setShow] = useState(false);
    const [error, setError] = useState(false);
    const [error2, setError2] = useState(false);

    const [textFieldValue, setTextFieldValue] = useState("");

    const handleTextFieldChange = useCallback(
        (value) => setTextFieldValue(value),
        []
    );
    const errorMessage = error ? "Please Enter Coupon Code First !" : "";
    const errorMessage2 = error2 ? "Please Enter Valid Coupon Code !" : "";

    const applyCode = async (value) => {
        // console.log(value);
        if (value === "") {
            setError(true);
            // errorMessage = "Please Enter Coupon Code First !";
            // console.log("value");
        } else {
            setError(false);
            // console.log('else');
            const res = await fetch(
                "/api/getCouponDetails/" +
                    document.getElementById("shopOrigin").value +
                    "/" +
                    value,
                {
                    method: "GET",
                }
            );
            const couponDetail = await res.json();
            console.log(couponDetail);
            if (couponDetail.data === null) {
                setError2(true);
            } else {
                setError2(false);
                setDiscount(couponDetail.data.discount);
                setSaving(couponDetail.data.saving);
                setTotal(couponDetail.data.finalPrice);
                setBasicUrl(couponDetail.data.basic_url);
                setShow(true);
            }
        }
    };

    const rows = [
        ["Subtotal", "$" + import.meta.env.VITE_APP_TOTAL_CHARGE],
        ["Discount", discount],
        ["You are saving", saving],
    ];

    const removeTag = () => {
        setShow(false);
        setTextFieldValue("");
        setTotal(import.meta.env.VITE_APP_TOTAL_CHARGE);
    };

    // console.log(shopDomain);
    //CHECK IF PLAN ID EXISTS
    useEffect(() => {
        const getPlanId = async () => {
            const res = await fetch(
                "/api/ifPlanIdExists/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const plan = await res.json();
            console.log(plan);
            setBasicUrl(plan.data.charges.basic_url);
            setTryOnUrl(plan.data.charges.try_url);
            setBasicButton(plan.data.charges.basic_button);
            setTryOnButton(plan.data.charges.try_button);
        };
        getPlanId();
    }, []);

    // console.log(basicUrl);
    return (
        <Card sectioned>
            <Banner title="DiamondLinkⓇ Powered By GemFind" status="info">
                <p className="mb-2">
                    Have a Coupon? Enter your Coupon code here.
                </p>
                <div className="mb-2">
                    <TextField
                        value={textFieldValue}
                        onChange={handleTextFieldChange}
                        placeholder="Discount Code"
                        autoComplete="off"
                        id={1}
                        connectedRight={
                            <Button
                                onClick={() => {
                                    applyCode(textFieldValue);
                                }}
                            >
                                Apply
                            </Button>
                        }
                    />
                </div>
                <div style={{ marginTop: "4px" }}>
                    <InlineError message={errorMessage} fieldID={1} />
                    <InlineError message={errorMessage2} fieldID={1} />
                </div>
                {show === true ? (
                    <>
                        <Tag
                            onRemove={() => {
                                removeTag();
                            }}
                        >
                            <Stack spacing="extraTight">
                                <Icon source={DiscountCodeMajor} />
                                <span>{textFieldValue}</span>
                            </Stack>
                        </Tag>
                        <DataTable
                            columnContentTypes={["text", "numeric"]}
                            headings={["", ""]}
                            rows={rows}
                            totals={["", "$" + total]}
                            showTotalsInFooter
                        />
                    </>
                ) : null}
            </Banner>

            <div className="maincontainer">
                <section>
                    <div className="container py-5">
                        <div className="row text-center justify-content-center">
                            <div className="col-lg-6 mb-5 mb-lg-0">
                                <div className="bg-white p-5 rounded-lg shadow">
                                    <h1 className="h3 font-weight-bold mb-4">
                                    DiamondLinkⓇ Powered By GemFind
                                    </h1>
                                    <h2 className="h1 font-weight-bold">
                                        ${total}
                                        <span className="text-small font-weight-normal ml-2">
                                            /month
                                        </span>
                                    </h2>
                                    <div className="custom-separator my-4 mx-auto bg-primary"></div>
                                    {/* <ul className="list-unstyled my-5 text-small text-left">
                                        <li className="mb-3">
                                            <i className="fa fa-check mr-2 text-primary"></i>{" "}
                                           DiamondLink Powered By GemFind
                                        </li>
                                    </ul> */}
                                    <a
                                        href={basicUrl}
                                        className="btn btn-primary btn-block p-2 shadow rounded-pill"
                                    >
                                        {basicButton}
                                    </a>
                                </div>
                            </div>
                            {/* <div className="col-lg-6 mb-5 mb-lg-0">
                                <div className="bg-white p-5 rounded-lg shadow">
                                    <h1 className="h3 text-uppercase font-weight-bold mb-4">
                                        TRY-ON PLAN
                                    </h1>
                                    <h2 className="h1 font-weight-bold">
                                        $345
                                        <span className="text-small font-weight-normal ml-2">
                                            / month
                                        </span>
                                    </h2>
                                    <div className="custom-separator my-4 mx-auto bg-primary"></div>
                                    <ul className="list-unstyled my-5 text-small text-left font-weight-normal">
                                        <li className="mb-3">
                                            <i className="fa fa-check mr-2 text-primary"></i>{" "}
                                        </li>
                                        <li className="mb-3">
                                            <i className="fa fa-check mr-2 text-primary"></i>{" "}
                                            TRY-ON plan provides the feature of
                                            TRY-ON along with Ring Builder Tool.
                                        </li>
                                    </ul>
                                    <a
                                        href={tryOnUrl}
                                        className="btn btn-primary btn-block p-2 shadow rounded-pill"
                                    >
                                        {tryOnButton}
                                    </a>
                                </div>
                            </div> */}
                        </div>
                    </div>
                </section>
            </div>
        </Card>
    );
}

export default Plan;
