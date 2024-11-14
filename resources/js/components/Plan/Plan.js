import { Card } from "@shopify/polaris";
import React, { useEffect, useState } from "react";
import "bootstrap/dist/css/bootstrap.min.css";
function Plan() {
    let shopDomain = document.getElementById("shopOrigin").value;
    const [basicUrl, setBasicUrl] = useState([]);
    const [tryOnUrl, setTryOnUrl] = useState([]);
    const [basicButton, setBasicButton] = useState([]);
    const [tryOnButton, setTryOnButton] = useState([]);

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
            <div className="maincontainer">
                <section>
                    <div className="container py-5">
                        <div className="row text-center align-items-end">
                            <div className="col-lg-6 mb-5 mb-lg-0">
                                <div className="bg-white p-5 rounded-lg shadow">
                                    <h1 className="h3 text-uppercase font-weight-bold mb-4">
                                        BASIC PLAN
                                    </h1>
                                    <h2 className="h1 font-weight-bold">
                                        $295
                                        <span className="text-small font-weight-normal ml-2">
                                            / month
                                        </span>
                                    </h2>
                                    <div className="custom-separator my-4 mx-auto bg-primary"></div>
                                    <ul className="list-unstyled my-5 text-small text-left">
                                        <li className="mb-3">
                                            <i className="fa fa-check mr-2 text-primary"></i>{" "}
                                            For TRY-ON - Additional $50/Month
                                        </li>
                                        <li className="mb-3">
                                            <i className="fa fa-check mr-2 text-primary"></i>{" "}
                                            Basic plan covers only Ring Builder
                                            without any Addon feature like
                                            TryOn.
                                        </li>
                                    </ul>
                                    <a
                                        href={basicUrl}
                                        className="btn btn-primary btn-block p-2 shadow rounded-pill"
                                    >
                                        {basicButton}
                                    </a>
                                </div>
                            </div>
                            <div className="col-lg-6 mb-5 mb-lg-0">
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
                                            {/* For TRY-ON - Additional $50/Month */}
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
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </Card>
    );
}

export default Plan;
