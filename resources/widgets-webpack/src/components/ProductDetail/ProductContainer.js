import React, { useEffect } from "react";
import { useState } from "react";
import styles from "./productDetail.module.css";
import { LoadingOverlay, Loader } from "react-overlay-loader";
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { Modal } from "react-responsive-modal";
import "react-responsive-modal/styles.css";
import TextField from "@material-ui/core/TextField";
import Radio from "@material-ui/core/Radio";
import RadioGroup from "@material-ui/core/RadioGroup";
import FormControlLabel from "@material-ui/core/FormControlLabel";
import FormControl from "@material-ui/core/FormControl";
import MenuItem from "@material-ui/core/MenuItem";
import Select from "@material-ui/core/Select";
import { useCookies } from "react-cookie";
import ReCAPTCHA from "react-google-recaptcha";

if (window.meta.product) {
    if (window.meta.product.type === "RingBuilderAdvance") {
        require("./removeCss.css");
    }
}

const ProductContainer = () => {
    const [ringValues, setRingValues] = useState([]);
    const [firstDropdownValue, setFirstDropdownValue] = useState("");
    const [getAllMetafields, setGetAllMetafields] = useState([]);
    const [getRingSize, setRingSize] = useState("");
    const [getInitData, setGetInitData] = useState([]);
    const [loaded, setLoaded] = useState(false);
    let errors = {};
    let formIsValid = true;
    const [cookies, setCookie] = useCookies(["_shopify_ringsetting"]);
    const [getdiamondcookies, setdiamondcookies] = useCookies([
        "_shopify_diamondsetting",
    ]);

    const [recaptchaToken, setRecaptchaToken] = useState("");
    const [isRecaptchaVerified, setIsRecaptchaVerified] = useState(false);

    const [recaptchaReqToken, setReqRecaptchaToken] = useState("");
    const [isReqRecaptchaVerified, setIsReqRecaptchaVerified] = useState(false);

    const [recaptchaEmailFrndToken, setEmailFrndRecaptchaToken] = useState("");
    const [isEmailFrndRecaptchaVerified, setIsEmailFrndRecaptchaVerified] =
        useState(false);

    const [recaptchaSchlToken, setSchlRecaptchaToken] = useState("");
    const [isSchlRecaptchaVerified, setIsSchlRecaptchaVerified] =
        useState(false);

    const [getDiamondCookie, setDiamondCookie] = useState(false);
    const [getsettingcookie, setsettingcookie] = useState(false);

    const [openSecond, setOpenSecond] = useState(false);
    const [openThird, setOpenThird] = useState(false);
    const [openFour, setOpenFour] = useState(false);
    const [openFive, setOpenFive] = useState(false);

    const onOpenSecondModal = (e) => {
        e.preventDefault();
        setyourname("");
        setyouremail("");
        setrecipientname("");
        setrecipientemail("");
        setgiftreason("");
        sethintmessage("");
        setgiftdeadline("");
        setOpenSecond(true);
    };
    const onOpenThirdModal = (e) => {
        e.preventDefault();
        setreqname("");
        setreqemail("");
        setreqphone("");
        setreqmsg("");
        setreqcp("");
        setOpenThird(true);
    };
    const onOpenFourthModal = (e) => {
        e.preventDefault();
        setname("");
        setemail("");
        setfrndname("");
        setfrndemail("");
        setfrndmessage("");
        setOpenFour(true);
    };
    const onOpenFifthModal = (e) => {
        e.preventDefault();
        setschdname("");
        setschdemail("");
        setschdphone("");
        setschdmsg("");
        setschddate("");
        setschdtime("");
        setLocation("");
        setOpenFive(true);
    };

    const handleRingSize = (e) => {
        e.preventDefault();
        setRingSize(e.target.value);
    };

    // Function to get value from the first dropdown dynamically
    const fetchFirstDropdownValue = () => {
        const firstDropdown = document.getElementById(
            "Option-template--22954725015861__main-1"
        );
        if (firstDropdown) {
            setFirstDropdownValue(firstDropdown.value); // Get the selected value from the dropdown
        }
    };

    // Use useEffect to fetch the value on component mount
    useEffect(() => {
        fetchFirstDropdownValue(); // Fetch the value when the component mounts

        // Optionally, you can add an event listener to handle changes in the first dropdown
        const firstDropdown = document.getElementById(
            "Option-template--22954725015861__main-1"
        );
        if (firstDropdown) {
            firstDropdown.addEventListener("change", fetchFirstDropdownValue);
        }

        // Cleanup listener when component unmounts
        return () => {
            if (firstDropdown) {
                firstDropdown.removeEventListener(
                    "change",
                    fetchFirstDropdownValue
                );
            }
        };
    }, []);

    //GET METAFIELDS API
    useEffect(() => {
        // setLoaded(true);
        if (
            getdiamondcookies._shopify_diamondsetting &&
            getdiamondcookies._shopify_diamondsetting[0].diamondId
        ) {
            setDiamondCookie(true);
        }
        if (
            cookies._shopify_ringsetting &&
            cookies._shopify_ringsetting[0].setting_id
        ) {
            setsettingcookie(true);
        }
        const getMetafieldsData = async () => {
            const res = await fetch(
                "https://raadj.app.theringbuilder.com/api/getMetafields/" +
                    window.Shopify.shop +
                    "/" +
                    window.meta.product.id,
                {
                    method: "POST",
                }
            );
            const metafields = await res.json();
            const ring_size = metafields.ringSize.value.split(",");
            setRingValues(ring_size);
            setGetAllMetafields(metafields);
        };
        getMetafieldsData();
        const getInitTool = async () => {
            const requestOptions = {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ shop_domain: window.Shopify.shop }),
            };
            try {
                const res = await fetch(
                    "https://raadj.app.theringbuilder.com/api/initToolApi",
                    requestOptions
                );
                const initData = await res.json();
                setGetInitData(initData.data[0]);
            } catch (error) {
                console.log(error);
            }
        };
        getInitTool();
    }, []);
    console.log(getAllMetafields);

    const handleRecaptchaChange = (response) => {
        setRecaptchaToken(response);
        setIsRecaptchaVerified(true); // Set verification status
    };

    const handleReqRecaptchaChange = (response) => {
        setReqRecaptchaToken(response);
        setIsReqRecaptchaVerified(true); // Set verification status
    };

    const handleEmailFrndRecaptchaChange = (response) => {
        setEmailFrndRecaptchaToken(response);
        setIsEmailFrndRecaptchaVerified(true); // Set verification status
    };

    const handleSchlRecaptchaChange = (response) => {
        setSchlRecaptchaToken(response);
        setIsSchlRecaptchaVerified(true); // Set verification status
    };

    //DROP HINT SUBMIT BUTTON
    const [getyourname, setyourname] = useState("");
    const [getyouremail, setyouremail] = useState("");
    const [getrecipientname, setrecipientname] = useState("");
    const [getrecipientemail, setrecipientemail] = useState("");
    const [getgiftreason, setgiftreason] = useState("");
    const [gethintmessage, sethintmessage] = useState("");
    const [getgiftdeadline, setgiftdeadline] = useState("");

    const [geterror, seterror] = useState([""]);

    const handleYourname = (event) => {
        setyourname(event.target.value);
    };
    const handleYouremail = (event) => {
        setyouremail(event.target.value);
    };
    const handleRecipientname = (event) => {
        setrecipientname(event.target.value);
    };
    const handleRecipientemail = (event) => {
        setrecipientemail(event.target.value);
    };
    const handleGiftreason = (event) => {
        setgiftreason(event.target.value);
    };
    const handleHintmessage = (event) => {
        sethintmessage(event.target.value);
    };
    const handleGiftdeadline = (event) => {
        setgiftdeadline(event.target.value);
    };

    const handledrophintSubmit = async (e) => {
        console.log(e);
        e.preventDefault();
        setLoaded(true);

        //Validation

        //Name
        if (getyourname === "") {
            errors["yourname"] = "Please enter your name";
            formIsValid = false;
        }
        if (getrecipientname === "") {
            errors["yourname"] = "Please enter your friend name";
            formIsValid = false;
        }

        //Email
        const regex =
            /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
        if (regex.test(getyouremail) === false) {
            errors["youremail"] = "Please enter valid email";
            formIsValid = false;
        }
        if (regex.test(getrecipientemail) === false) {
            errors["youremail"] = "Please enter valid email";
            formIsValid = false;
        }

        //Reason
        if (getgiftreason === "") {
            errors["yourreason"] = "Please enter your reason";
            formIsValid = false;
        }

        //Message
        if (gethintmessage === "") {
            errors["yourmsg"] = "Please enter your message";
            formIsValid = false;
        }

        //Deadline
        if (getgiftdeadline === "") {
            errors["yourdeadline"] = "Please enter your deadline";
            formIsValid = false;
        }

        if (getInitData.google_site_key && getInitData.google_secret_key) {
            if (recaptchaToken === "") {
                errors["yourrecaptcha"] =
                    "The recaptcha token field is required.";
                formIsValid = false;
            }
        }

        if (formIsValid == false) {
            console.log(errors);
            seterror(errors);
            setLoaded(false);
            return;
        }

        const requestOptions1 = {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                name: getyourname,
                email: getyouremail,
                hint_Recipient_name: getrecipientname,
                hint_Recipient_email: getrecipientemail,
                reason_of_gift: getgiftreason,
                hint_message: gethintmessage,
                deadline: getgiftdeadline,
                ring_url: window.location.href,
                settingid: window.meta.product.id,
                islabsettings: getAllMetafields.islabsettings
                    ? getAllMetafields.islabsettings.value
                    : "false",
                shopurl: getInitData.shop,
                currency: getInitData.currency,
                recaptchaToken: recaptchaToken,
            }),
        };
        try {
            const res = await fetch(
                `${getInitData.server_url}/api/dropHintApi`,
                requestOptions1
            );
            const hintData = await res.json();
            setOpenSecond(false);
            toast("Email Send Successfully");
            setLoaded(false);
            setyourname("");
            setyouremail("");
            setrecipientname("");
            setrecipientemail("");
            setgiftreason("");
            sethintmessage("");
            setgiftdeadline("");
            //console.log(hintData);
        } catch (error) {
            //console.log();
        }
    };

    const handleadddiamonds = (e) => {
        setLoaded(true);
        e.preventDefault();
        var ringData = [];
        var data = {};
        if (getRingSize === "") {
            data.ringsizewithdia = document.getElementById("ring_size").value;
        } else {
            data.ringsizewithdia = getRingSize;
        }
        data.ringmaxcarat = getAllMetafields.MaximumCarat.value
            ? getAllMetafields.MaximumCarat.value
            : "";
        data.ringmincarat = getAllMetafields.MinimumCarat.value
            ? getAllMetafields.MinimumCarat.value
            : "";
        data.centerStoneFit = getAllMetafields.shape.value
            ? getAllMetafields.shape.value
            : "";
        data.centerStoneSize = "";
        data.sideStoneQuality = "";
        data.setting_id = document.getElementsByName("id")[0].value;
        data.isLabSetting = getAllMetafields.islabsettings
            ? getAllMetafields.islabsettings.value
            : "false";
        data.ringpath = window.location.href;
        data.product_id = window.meta.product.id;
        console.log(data);
        ringData.push(data);
        setCookie("_shopify_ringsetting", JSON.stringify(ringData), {
            path: "/",
            maxAge: 604800,
        });
        var redirect_url =
            "https://" +
            window.Shopify.shop +
            "/apps/engagement-rings/navlabgrown";
        //location.replace(redirect_url)
        window.location.href = redirect_url;
    };

    const handleCompletering = (e) => {
        setLoaded(true);
        e.preventDefault();
        var ringData = [];
        var data = {};
        if (getRingSize === "") {
            data.ringsizewithdia = document.getElementById("ring_size").value;
        } else {
            data.ringsizewithdia = getRingSize;
        }
        data.ringmaxcarat = getAllMetafields.MaximumCarat.value
            ? getAllMetafields.MaximumCarat.value
            : "";
        data.ringmincarat = getAllMetafields.MinimumCarat.value
            ? getAllMetafields.MinimumCarat.value
            : "";
        data.centerStoneFit = getAllMetafields.shape.value
            ? getAllMetafields.shape.value
            : "";
        data.centerStoneSize = "";
        data.sideStoneQuality = "";
        data.setting_id = document.getElementsByName("id")[0].value;
        data.isLabSetting = getAllMetafields.islabsettings
            ? getAllMetafields.islabsettings.value
            : "false";
        data.ringpath = window.location.href;
        data.product_id = window.meta.product.id;
        //console.log(data);
        ringData.push(data);
        setCookie("_shopify_ringsetting", JSON.stringify(ringData), {
            path: "/",
            maxAge: 604800,
        });
        var redirect_url =
            "https://" +
            window.Shopify.shop +
            "/apps/engagement-rings/completering";
        //location.replace(redirect_url)
        window.location.href = redirect_url;
    };

    //REQUEST MORE INFORMATION SUBMIT BUTTON

    const [getreqname, setreqname] = useState("");
    const [getreqemail, setreqemail] = useState("");
    const [getreqphone, setreqphone] = useState("");
    const [getreqmsg, setreqmsg] = useState("");
    const [getreqcp, setreqcp] = useState("");

    const [getreqerror, setreqerror] = useState([""]);

    const handleReqname = (event) => {
        setreqname(event.target.value);
    };
    const handleReqemail = (event) => {
        setreqemail(event.target.value);
    };
    const handleReqphone = (event) => {
        setreqphone(event.target.value);
    };
    const handleReqmsg = (event) => {
        setreqmsg(event.target.value);
    };
    const handleReqcp = (event) => {
        setreqcp(event.target.value);
    };

    const handlereginfoSubmit = async (e) => {
        var selectedVariantUrl = window.location.href;
        var splitVariant = selectedVariantUrl.split("variant=");
        var variant = splitVariant[1];
        if (variant) {
            var variantId = variant;
        } else {
            var variantId = window.meta.product.variants[0]["id"];
        }
        var productType = window.meta.product.type;
        if (getAllMetafields.MaximumCarat.value) {
            var max_carat = getAllMetafields.MaximumCarat.value;
        } else {
            var max_carat = "";
        }
        if (getAllMetafields.MaximumCarat.value) {
            var min_carat = getAllMetafields.MinimumCarat.value;
        } else {
            var min_carat = "";
        }
        e.preventDefault();
        setLoaded(true);

        //Validation

        //Name
        if (getreqname === "") {
            errors["yourname"] = "Please enter your name";
            formIsValid = false;
        }

        //Email
        const regex =
            /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
        if (regex.test(getreqemail) === false) {
            errors["youremail"] = "Please enter valid email";
            formIsValid = false;
        }

        //Phone no.
        var pattern = new RegExp(/^[0-9\b]+$/);
        if (!pattern.test(getreqphone)) {
            errors["yourphone"] = "Please enter only number";
            formIsValid = false;
        } else if (getreqphone.length != 10) {
            errors["yourphone"] = "Please enter valid phone number.";
            formIsValid = false;
        }

        //Message
        if (getreqmsg === "") {
            errors["yourmsg"] = "Please enter your message";
            formIsValid = false;
        }

        //Contact Preference
        if (getreqcp === "") {
            errors["yourcp"] = "Please select contact preference";
            formIsValid = false;
        }

        if (getInitData.google_site_key && getInitData.google_secret_key) {
            if (recaptchaReqToken === "") {
                errors["yourreqrecaptcha"] =
                    "The recaptcha token field is required.";
                formIsValid = false;
            }
        }

        if (formIsValid == false) {
            console.log(errors);
            setreqerror(errors);
            setLoaded(false);
            return;
        }

        const requestOptions2 = {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                name: getreqname,
                email: getreqemail,
                phone_no: getreqphone,
                message: getreqmsg,
                contact_preference: getreqcp,
                ring_url: window.location.href,
                settingid: window.meta.product.id,
                islabsettings: getAllMetafields.islabsettings
                    ? getAllMetafields.islabsettings.value
                    : "false",
                shopurl: getInitData.shop,
                currency: getInitData.currency,
                variantId: variantId,
                productType: productType,
                max_carat: max_carat,
                min_carat: min_carat,
                recaptchaToken: recaptchaReqToken,
            }),
        };
        try {
            const res = await fetch(
                `${getInitData.server_url}/api/reqInfoApi`,
                requestOptions2
            );
            const hintData = await res.json();
            setOpenThird(false);
            toast("Email Send Successfully");
            setLoaded(false);
            setyourname("");
            setyouremail("");
            setrecipientname("");
            setrecipientemail("");
            setgiftreason("");
            sethintmessage("");
            setgiftdeadline("");
            //console.log(hintData);
        } catch (error) {
            //console.log();
        }
    };

    //EMAIL A FRIENDS SUBMIT BUTTON
    const [getname, setname] = useState("");
    const [getemail, setemail] = useState("");
    const [getfrndname, setfrndname] = useState("");
    const [getfrndemail, setfrndemail] = useState("");
    const [getfrndmessage, setfrndmessage] = useState("");

    const [getfrnderror, setfrnderror] = useState([""]);

    const handleName = (event) => {
        setname(event.target.value);
    };
    const handleEmail = (event) => {
        setemail(event.target.value);
    };
    const handleFrndname = (event) => {
        setfrndname(event.target.value);
    };
    const handleFrndemail = (event) => {
        setfrndemail(event.target.value);
    };
    const handleFrndmessage = (event) => {
        setfrndmessage(event.target.value);
    };

    const handleemailfrndSubmit = async (e) => {
        var selectedVariantUrl = window.location.href;
        var splitVariant = selectedVariantUrl.split("variant=");
        var variant = splitVariant[1];
        if (variant) {
            var variantId = variant;
        } else {
            var variantId = window.meta.product.variants[0]["id"];
        }
        var productType = window.meta.product.type;
        if (getAllMetafields.MaximumCarat.value) {
            var max_carat = getAllMetafields.MaximumCarat.value;
        } else {
            var max_carat = "";
        }
        if (getAllMetafields.MaximumCarat.value) {
            var min_carat = getAllMetafields.MinimumCarat.value;
        } else {
            var min_carat = "";
        }
        e.preventDefault();
        setLoaded(true);

        //Validation

        //Name
        if (getname === "") {
            errors["yourname"] = "Please enter your name";
            formIsValid = false;
        }
        if (getfrndname === "") {
            errors["yourname"] = "Please enter your friend name";
            formIsValid = false;
        }

        //Email
        const regex =
            /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
        if (regex.test(getemail) === false) {
            errors["youremail"] = "Please enter valid email";
            formIsValid = false;
        }
        if (regex.test(getfrndemail) === false) {
            errors["youremail"] = "Please enter valid email";
            formIsValid = false;
        }

        //Message
        if (getfrndmessage === "") {
            errors["yourmsg"] = "Please enter your message";
            formIsValid = false;
        }

        if (getInitData.google_site_key && getInitData.google_secret_key) {
            if (recaptchaEmailFrndToken === "") {
                errors["yourfrndrecaptcha"] =
                    "The recaptcha token field is required.";
                formIsValid = false;
            }
        }

        if (formIsValid == false) {
            console.log(errors);
            setfrnderror(errors);
            setLoaded(false);
            return;
        }

        const requestOptions3 = {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                name: getname,
                email: getemail,
                frnd_name: getfrndname,
                frnd_email: getfrndemail,
                frnd_message: getfrndmessage,
                ring_url: window.location.href,
                settingid: window.meta.product.id,
                islabsettings: getAllMetafields.islabsettings
                    ? getAllMetafields.islabsettings.value
                    : "false",
                shopurl: getInitData.shop,
                currency: getInitData.currency,
                variantId: variantId,
                productType: productType,
                max_carat: max_carat,
                min_carat: min_carat,
                recaptchaToken: recaptchaEmailFrndToken,
            }),
        };
        try {
            const res = await fetch(
                `${getInitData.server_url}/api/emailFriendApi`,
                requestOptions3
            );
            const hintData = await res.json();
            setOpenFour(false);
            toast("Email Send Successfully");
            setLoaded(false);
            setyourname("");
            setyouremail("");
            setrecipientname("");
            setrecipientemail("");
            setgiftreason("");
            sethintmessage("");
            setgiftdeadline("");
            //console.log(hintData);
        } catch (error) {
            //console.log();
        }
    };
    //SCHEDULE VIWING SUBMIT BUTTON

    const [getschdname, setschdname] = useState("");
    const [getschdemail, setschdemail] = useState("");
    const [getschdphone, setschdphone] = useState("");
    const [getschdmsg, setschdmsg] = useState("");
    const [getschddate, setschddate] = useState("");
    const [getschdtime, setschdtime] = useState("");
    const [location, setLocation] = useState("");

    const [getschderror, setschderror] = useState([""]);
    const [getblankvalue, setblankvalue] = useState([""]);

    const handleSchdname = (event) => {
        setschdname(event.target.value);
    };
    const handleSchdemail = (event) => {
        setschdemail(event.target.value);
    };
    const handleSchdphone = (event) => {
        setschdphone(event.target.value);
    };
    const handleSchdmsg = (event) => {
        setschdmsg(event.target.value);
    };
    const handleSchddate = (event) => {
        setschddate(event.target.value);
    };
    const handleSchdtime = (event) => {
        setschdtime(event.target.value);
    };
    const handleChange = (event) => {
        setLocation(event.target.value);
    };

    const handleschdSubmit = async (e) => {
        var selectedVariantUrl = window.location.href;
        var splitVariant = selectedVariantUrl.split("variant=");
        var variant = splitVariant[1];
        if (variant) {
            var variantId = variant;
        } else {
            var variantId = window.meta.product.variants[0]["id"];
        }
        var productType = window.meta.product.type;
        if (getAllMetafields.MaximumCarat.value) {
            var max_carat = getAllMetafields.MaximumCarat.value;
        } else {
            var max_carat = "";
        }
        if (getAllMetafields.MaximumCarat.value) {
            var min_carat = getAllMetafields.MinimumCarat.value;
        } else {
            var min_carat = "";
        }
        e.preventDefault();
        setLoaded(true);

        //Validation

        //Name
        if (getschdname === "") {
            errors["yourname"] = "Please enter your name";
            formIsValid = false;
        }

        //Email
        const regex =
            /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
        if (regex.test(getschdemail) === false) {
            errors["youremail"] = "Please enter valid email";
            formIsValid = false;
        }

        //Phone no.
        var pattern = new RegExp(/^[0-9\b]+$/);
        if (!pattern.test(getschdphone)) {
            errors["yourphone"] = "Please enter only number";
            formIsValid = false;
        } else if (getschdphone.length != 10) {
            errors["yourphone"] = "Please enter valid phone number.";
            formIsValid = false;
        }

        //Message
        if (getschdmsg === "") {
            errors["yourmsg"] = "Please enter your message";
            formIsValid = false;
        }

        //Location
        if (location === "") {
            errors["yourlocation"] = "Please select your location";
            formIsValid = false;
        }

        //Availibilty Date
        if (getschddate === "") {
            errors["yourdate"] = "Please select your availibility date";
            formIsValid = false;
        }

        if (getInitData.google_site_key && getInitData.google_secret_key) {
            if (recaptchaSchlToken === "") {
                errors["yourscrecaptcha"] =
                    "The recaptcha token field is required.";
                formIsValid = false;
            }
        }

        if (formIsValid == false) {
            console.log(errors);
            setschderror(errors);
            setLoaded(false);
            return;
        }

        const requestOptions4 = {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                name: getschdname,
                email: getschdemail,
                phone_no: getschdphone,
                schl_message: getschdmsg,
                location: location,
                availability_date: getschddate,
                appnt_time: getschdtime,
                ring_url: window.location.href,
                settingid: window.meta.product.id,
                islabsettings: getAllMetafields.islabsettings
                    ? getAllMetafields.islabsettings.value
                    : "false",
                shopurl: getInitData.shop,
                currency: getInitData.currency,
                variantId: variantId,
                productType: productType,
                max_carat: max_carat,
                min_carat: min_carat,
                recaptchaToken: recaptchaSchlToken,
            }),
        };
        try {
            const res = await fetch(
                `${getInitData.server_url}/api/scheViewApi`,
                requestOptions4
            );
            const hintData = await res.json();
            setOpenFive(false);
            toast("Email Send Successfully");
            setLoaded(false);
            setyourname("");
            setyouremail("");
            setrecipientname("");
            setrecipientemail("");
            setgiftreason("");
            sethintmessage("");
            setgiftdeadline("");
            //console.log(hintData);
        } catch (error) {
            //console.log();
        }
    };

    if (window.meta.product) {
        if (window.meta.product.type === "RingBuilderAdvance") {
            return (
                <>
                    <ToastContainer
                        position="top-center"
                        autoClose={5000}
                        hideProgressBar={false}
                        newestOnTop={false}
                        closeOnClick
                        rtl={false}
                        pauseOnFocusLoss
                        draggable
                        pauseOnHover
                    />
                    <div className={`${styles.productDropdown}`}>
                        <span>Ring Size</span>

                        <input
                            type="hidden"
                            name="ring_size"
                            id="ring_size"
                            className={styles.ringdDropdown}
                            value={firstDropdownValue} // Store the first dropdown's value
                            readOnly
                        />
                    </div>

                    <div className="ring-descreption">
                        <LoadingOverlay className="loading_overlay_wrapper">
                            <Loader fullPage loading={loaded} />
                        </LoadingOverlay>
                        <div className={styles.productController}>
                            <ul>
                                {getInitData.enable_hint === "1" && (
                                    <li>
                                        <a
                                            href="#!"
                                            onClick={onOpenSecondModal}
                                        >
                                            <span>
                                                <i className="fas fa-gift"></i>
                                            </span>
                                            Drop A Hint
                                        </a>
                                        <Modal
                                            open={openSecond}
                                            onClose={() => setOpenSecond(false)}
                                            center
                                            classNames={{
                                                overlay: "popup_Overlay",
                                                modal: "popup-form",
                                            }}
                                        >
                                            <LoadingOverlay className="_loading_overlay_wrapper">
                                                <Loader
                                                    fullPage
                                                    loading={loaded}
                                                />
                                            </LoadingOverlay>
                                            <div className="Diamond-form">
                                                <div className="requested-form">
                                                    <h2>Drop A Hint</h2>
                                                    <p>
                                                        Because you deserve
                                                        this.
                                                    </p>
                                                </div>
                                                <form
                                                    onSubmit={
                                                        handledrophintSubmit
                                                    }
                                                >
                                                    <div className="form-field">
                                                        <TextField
                                                            id="drophint_name"
                                                            label="Your Name"
                                                            variant="outlined"
                                                            focused
                                                            value={getyourname}
                                                            onChange={
                                                                handleYourname
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                geterror.yourname
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="drophint_email"
                                                            type="email"
                                                            label="Your E-mail"
                                                            variant="outlined"
                                                            focused
                                                            value={getyouremail}
                                                            onChange={
                                                                handleYouremail
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                geterror.youremail
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="drophint_rec_name"
                                                            label="Hint Recipient's Name"
                                                            variant="outlined"
                                                            focused
                                                            value={
                                                                getrecipientname
                                                            }
                                                            onChange={
                                                                handleRecipientname
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                geterror.yourname
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="drophint_rec_email"
                                                            type="email"
                                                            label="Hint Recipient's E-mail"
                                                            focused
                                                            variant="outlined"
                                                            value={
                                                                getrecipientemail
                                                            }
                                                            onChange={
                                                                handleRecipientemail
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                geterror.youremail
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="dgift_reason"
                                                            label="Reason For This Gift"
                                                            variant="outlined"
                                                            focused
                                                            value={
                                                                getgiftreason
                                                            }
                                                            onChange={
                                                                handleGiftreason
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                geterror.yourreason
                                                            }{" "}
                                                        </p>

                                                        <TextField
                                                            id="drophint_message"
                                                            multiline
                                                            rows={3}
                                                            label="Add A Personal Message Here ..."
                                                            focused
                                                            variant="outlined"
                                                            value={
                                                                gethintmessage
                                                            }
                                                            onChange={
                                                                handleHintmessage
                                                            }
                                                        />

                                                        <p>
                                                            {" "}
                                                            {
                                                                geterror.yourmsg
                                                            }{" "}
                                                        </p>

                                                        <TextField
                                                            label="Gift Deadline"
                                                            id="date"
                                                            type="date"
                                                            inputformat="MM/dd/yyyy"
                                                            focused
                                                            variant="outlined"
                                                            InputLabelProps={{
                                                                shrink: true,
                                                            }}
                                                            value={
                                                                getgiftdeadline
                                                            }
                                                            onChange={
                                                                handleGiftdeadline
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                geterror.yourdeadline
                                                            }{" "}
                                                        </p>

                                                        <div className="prefrence-action">
                                                            <div className="prefrence-action action moveUp">
                                                                {getInitData.google_site_key &&
                                                                    getInitData.google_secret_key && (
                                                                        <div className="gf-grecaptcha">
                                                                            <ReCAPTCHA
                                                                                sitekey={
                                                                                    getInitData.google_site_key
                                                                                }
                                                                                onChange={
                                                                                    handleRecaptchaChange
                                                                                }
                                                                            />

                                                                            <p>
                                                                                {
                                                                                    geterror.yourrecaptcha
                                                                                }
                                                                            </p>
                                                                        </div>
                                                                    )}
                                                                <button
                                                                    type="submit"
                                                                    title="Submit"
                                                                    className="btn button preference-btn"
                                                                >
                                                                    <span>
                                                                        Drop
                                                                        Hint
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </Modal>
                                    </li>
                                )}

                                {getInitData.enable_more_info === "1" && (
                                    <li>
                                        <a href="#!" onClick={onOpenThirdModal}>
                                            <span>
                                                <i className="fas fa-info"></i>
                                            </span>
                                            Request More Info
                                        </a>
                                        <Modal
                                            open={openThird}
                                            onClose={() => setOpenThird(false)}
                                            center
                                            classNames={{
                                                overlay: "popup_Overlay",
                                                modal: "popup-form-small",
                                            }}
                                        >
                                            <LoadingOverlay className="_loading_overlay_wrapper">
                                                <Loader
                                                    fullPage
                                                    loading={loaded}
                                                />
                                            </LoadingOverlay>
                                            <div className="Diamond-form--small">
                                                <div className="requested-form">
                                                    <h2>
                                                        {" "}
                                                        REQUEST MORE INFORMATION
                                                    </h2>
                                                    <p>
                                                        Our specialists will
                                                        contact you.
                                                    </p>
                                                </div>
                                                <form
                                                    onSubmit={
                                                        handlereginfoSubmit
                                                    }
                                                    className="request-form"
                                                >
                                                    <div className="form-field">
                                                        <TextField
                                                            id="request_name"
                                                            label="Your Name"
                                                            focused
                                                            variant="outlined"
                                                            value={getreqname}
                                                            onChange={
                                                                handleReqname
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getreqerror.yourname
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="request_email"
                                                            type="email"
                                                            label="Your E-mail"
                                                            focused
                                                            variant="outlined"
                                                            value={getreqemail}
                                                            onChange={
                                                                handleReqemail
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getreqerror.youremail
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="request_phone"
                                                            label="Your Phone Number"
                                                            focused
                                                            variant="outlined"
                                                            value={getreqphone}
                                                            onChange={
                                                                handleReqphone
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getreqerror.yourphone
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="req_message"
                                                            multiline
                                                            rows={3}
                                                            label="Add A Personal Message Here ..."
                                                            focused
                                                            variant="outlined"
                                                            value={getreqmsg}
                                                            onChange={
                                                                handleReqmsg
                                                            }
                                                        />

                                                        <p>
                                                            {" "}
                                                            {
                                                                getreqerror.yourmsg
                                                            }{" "}
                                                        </p>

                                                        <div className="contact-prefrtence">
                                                            <span>
                                                                Contact
                                                                Preference:
                                                            </span>
                                                            <div className="pref_container">
                                                                <FormControl>
                                                                    <RadioGroup
                                                                        aria-labelledby="demo-radio-buttons-group-label"
                                                                        defaultValue="female"
                                                                        name="radio-buttons-group"
                                                                        value={
                                                                            getreqcp
                                                                        }
                                                                        onChange={
                                                                            handleReqcp
                                                                        }
                                                                    >
                                                                        <FormControlLabel
                                                                            value="By Email"
                                                                            name="contact_pref"
                                                                            control={
                                                                                <Radio />
                                                                            }
                                                                            label="By Email"
                                                                        />
                                                                        <FormControlLabel
                                                                            value="By Phone"
                                                                            name="contact_pref"
                                                                            control={
                                                                                <Radio />
                                                                            }
                                                                            label="By Phone"
                                                                        />
                                                                    </RadioGroup>
                                                                </FormControl>
                                                            </div>
                                                        </div>
                                                        <p>
                                                            {" "}
                                                            {getreqerror.yourcp}
                                                        </p>
                                                        <div className="prefrence-action">
                                                            <div className="prefrence-action action moveUp">
                                                                {getInitData.google_site_key &&
                                                                    getInitData.google_secret_key && (
                                                                        <div className="gf-grecaptcha">
                                                                            <ReCAPTCHA
                                                                                sitekey={
                                                                                    getInitData.google_site_key
                                                                                }
                                                                                onChange={
                                                                                    handleReqRecaptchaChange
                                                                                }
                                                                            />
                                                                            <p>
                                                                                {
                                                                                    getreqerror.yourreqrecaptcha
                                                                                }
                                                                            </p>
                                                                        </div>
                                                                    )}
                                                                <button
                                                                    type="submit"
                                                                    title="Submit"
                                                                    className="btn  button preference-btn"
                                                                >
                                                                    <span>
                                                                        Request
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </Modal>
                                    </li>
                                )}
                                {getInitData.enable_email_friend === "1" && (
                                    <li>
                                        <a
                                            href="#!"
                                            onClick={onOpenFourthModal}
                                        >
                                            <span>
                                                <i className="fas fa-envelope"></i>
                                            </span>
                                            E-Mail A Friend
                                        </a>
                                        <Modal
                                            open={openFour}
                                            onClose={() => setOpenFour(false)}
                                            center
                                            classNames={{
                                                overlay: "popup_Overlay",
                                                modal: "popup-form-extra-small",
                                            }}
                                        >
                                            <LoadingOverlay className="_loading_overlay_wrapper">
                                                <Loader
                                                    fullPage
                                                    loading={loaded}
                                                />
                                            </LoadingOverlay>
                                            <div className="Diamond-form--xx-small">
                                                <div className="requested-form">
                                                    <h2> E-MAIL A FRIEND</h2>
                                                </div>
                                                <form
                                                    onSubmit={
                                                        handleemailfrndSubmit
                                                    }
                                                    className="email-form"
                                                >
                                                    <div className="form-field">
                                                        <TextField
                                                            id="your_name"
                                                            label="Your Name"
                                                            variant="outlined"
                                                            focused
                                                            value={getname}
                                                            onChange={
                                                                handleName
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getfrnderror.yourname
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="your_email"
                                                            type="email"
                                                            label="Your E-mail"
                                                            variant="outlined"
                                                            focused
                                                            value={getemail}
                                                            onChange={
                                                                handleEmail
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getfrnderror.youremail
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="fri_name"
                                                            label="Your Friend's Name"
                                                            variant="outlined"
                                                            focused
                                                            value={getfrndname}
                                                            onChange={
                                                                handleFrndname
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getfrnderror.yourname
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="f_email"
                                                            type="email"
                                                            label="Your Friend's E-mail"
                                                            variant="outlined"
                                                            focused
                                                            value={getfrndemail}
                                                            onChange={
                                                                handleFrndemail
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getfrnderror.youremail
                                                            }{" "}
                                                        </p>
                                                        <TextField
                                                            id="email-fri_message"
                                                            multiline
                                                            rows={3}
                                                            label="Add A Personal Message Here ..."
                                                            focused
                                                            variant="outlined"
                                                            value={
                                                                getfrndmessage
                                                            }
                                                            onChange={
                                                                handleFrndmessage
                                                            }
                                                        />

                                                        <p>
                                                            {" "}
                                                            {
                                                                getreqerror.yourmsg
                                                            }{" "}
                                                        </p>

                                                        <div className="prefrence-action">
                                                            <div className="prefrence-action action moveUp">
                                                                {getInitData.google_site_key &&
                                                                    getInitData.google_secret_key && (
                                                                        <div className="gf-grecaptcha">
                                                                            <ReCAPTCHA
                                                                                sitekey={
                                                                                    getInitData.google_site_key
                                                                                }
                                                                                onChange={
                                                                                    handleEmailFrndRecaptchaChange
                                                                                }
                                                                            />
                                                                            <p>
                                                                                {
                                                                                    getfrnderror.yourfrndrecaptcha
                                                                                }
                                                                            </p>
                                                                        </div>
                                                                    )}
                                                                <button
                                                                    type="submit"
                                                                    title="Submit"
                                                                    className="btn  button preference-btn"
                                                                >
                                                                    <span>
                                                                        Send To
                                                                        Friend
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </Modal>
                                    </li>
                                )}
                                {getInitData.enable_schedule_viewing ===
                                    "1" && (
                                    <li>
                                        <a href="#!" onClick={onOpenFifthModal}>
                                            <span>
                                                <i className="far fa-calendar-alt"></i>
                                            </span>
                                            Schedule Viewing
                                        </a>
                                        <Modal
                                            open={openFive}
                                            onClose={() => setOpenFive(false)}
                                            center
                                            classNames={{
                                                overlay: "popup_Overlay",
                                                modal: "popup-form",
                                            }}
                                        >
                                            <LoadingOverlay className="_loading_overlay_wrapper">
                                                <Loader
                                                    fullPage
                                                    loading={loaded}
                                                />
                                            </LoadingOverlay>

                                            <div className="Diamond-form">
                                                <div className="requested-form">
                                                    <h2>SCHEDULE A VIEWING</h2>
                                                    <p>
                                                        See This Item And More
                                                        In Our Store.
                                                    </p>
                                                </div>
                                                <form
                                                    onSubmit={handleschdSubmit}
                                                    className="schedule-form"
                                                >
                                                    <div className="form-field">
                                                        <TextField
                                                            id="schedule_name"
                                                            label="Your Name"
                                                            focused
                                                            variant="outlined"
                                                            value={getschdname}
                                                            onChange={
                                                                handleSchdname
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getschderror.yourname
                                                            }{" "}
                                                        </p>

                                                        <TextField
                                                            id="schedule_email"
                                                            type="email"
                                                            label="Your E-mail Address"
                                                            focused
                                                            variant="outlined"
                                                            value={getschdemail}
                                                            onChange={
                                                                handleSchdemail
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getschderror.youremail
                                                            }{" "}
                                                        </p>

                                                        <TextField
                                                            id="schedule_num"
                                                            label="Your Phone Number"
                                                            focused
                                                            variant="outlined"
                                                            value={getschdphone}
                                                            onChange={
                                                                handleSchdphone
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getschderror.yourphone
                                                            }{" "}
                                                        </p>

                                                        <TextField
                                                            id="drophint_message"
                                                            multiline
                                                            rows={3}
                                                            label="Add A Personal Message Here ..."
                                                            focused
                                                            variant="outlined"
                                                            value={getschdmsg}
                                                            onChange={
                                                                handleSchdmsg
                                                            }
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getschderror.yourmsg
                                                            }{" "}
                                                        </p>

                                                        <Select
                                                            labelId="demo-controlled-open-select-label"
                                                            id="demo-controlled-open-select"
                                                            focused
                                                            // defaultValue={10}
                                                            value={location}
                                                            onChange={
                                                                handleChange
                                                            }
                                                            label="Location"
                                                            variant="outlined"
                                                        >
                                                            <MenuItem
                                                                value={"Test"}
                                                            >
                                                                Test
                                                            </MenuItem>
                                                            <MenuItem
                                                                value={
                                                                    "Newport beach123"
                                                                }
                                                            >
                                                                Newport beach123
                                                            </MenuItem>
                                                        </Select>
                                                        <p>
                                                            {" "}
                                                            {
                                                                getschderror.yourlocation
                                                            }{" "}
                                                        </p>

                                                        <TextField
                                                            label="When are you available?"
                                                            id="date"
                                                            type="date"
                                                            inputformat="MM/dd/yyyy"
                                                            focused
                                                            variant="outlined"
                                                            value={getschddate}
                                                            onChange={
                                                                handleSchddate
                                                            }
                                                            InputLabelProps={{
                                                                shrink: true,
                                                            }}
                                                        />
                                                        <p>
                                                            {" "}
                                                            {
                                                                getschderror.yourdate
                                                            }{" "}
                                                        </p>

                                                        <Select
                                                            labelId="demo-simple-select-standard-label"
                                                            id="select_time"
                                                            label="Time"
                                                            focused
                                                            variant="outlined"
                                                            value={getschdtime}
                                                            onChange={
                                                                handleSchdtime
                                                            }
                                                        >
                                                            <MenuItem
                                                                value={"08:00"}
                                                            >
                                                                08:00
                                                            </MenuItem>
                                                            <MenuItem
                                                                value={"08:30"}
                                                            >
                                                                08:30
                                                            </MenuItem>
                                                            <MenuItem
                                                                value={"09:00"}
                                                            >
                                                                09:00
                                                            </MenuItem>
                                                            <MenuItem
                                                                value={"09:30"}
                                                            >
                                                                09:30
                                                            </MenuItem>
                                                        </Select>

                                                        <div className="prefrence-action">
                                                            <div className="prefrence-action action moveUp">
                                                                {getInitData.google_site_key &&
                                                                    getInitData.google_secret_key && (
                                                                        <div className="gf-grecaptcha">
                                                                            <ReCAPTCHA
                                                                                sitekey={
                                                                                    getInitData.google_site_key
                                                                                }
                                                                                onChange={
                                                                                    handleSchlRecaptchaChange
                                                                                }
                                                                            />
                                                                            <p>
                                                                                {
                                                                                    getschderror.yourscrecaptcha
                                                                                }
                                                                            </p>
                                                                        </div>
                                                                    )}
                                                                <button
                                                                    type="submit"
                                                                    title="Submit"
                                                                    className="btn  button preference-btn"
                                                                >
                                                                    <span>
                                                                        Request
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </Modal>
                                    </li>
                                )}
                            </ul>
                        </div>
                    </div>
                    <div className="diamond-btn ">
                        {getsettingcookie === true &&
                        getDiamondCookie === true ? (
                            <button
                                type="submit"
                                title="Submit"
                                onClick={handleCompletering}
                                className="btn btn-diamond button button--full-width button--secondary"
                            >
                                Complete Your Ring
                            </button>
                        ) : (
                            getDiamondCookie === true && (
                                <button
                                    type="submit"
                                    title="Submit"
                                    onClick={handleCompletering}
                                    className="btn btn-tryon button button--full-width button--secondary"
                                >
                                    Complete Your Ring
                                </button>
                            )
                        )}

                        {getDiamondCookie === false && (
                            <button
                                type="submit"
                                title="Submit"
                                onClick={handleadddiamonds}
                                className="btn btn-diamond button button--full-width button--secondary"
                            >
                                Add Your Diamond
                            </button>
                        )}
                    </div>
                    <div className={styles.allMetafields}>
                        <span class="table-title">Additional Information</span>
                    </div>
                    <div className={styles.ringBuilderAdditional}>
                        <table class="table">
                            <tbody>
                                {getAllMetafields.GemstoneCaratWeight1 &&
                                    getAllMetafields.GemstoneCaratWeight1
                                        .value !== "" && (
                                        <tr>
                                            <th scope="row">
                                                GemstoneCaratWeight1
                                            </th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .GemstoneCaratWeight1
                                                        .value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.GemstoneCaratWeight2 &&
                                    getAllMetafields.GemstoneCaratWeight2
                                        .value !== "" && (
                                        <tr>
                                            <th scope="row">
                                                GemstoneCaratWeight2
                                            </th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .GemstoneCaratWeight2
                                                        .value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.GemstoneQuality1 &&
                                    getAllMetafields.GemstoneQuality1.value !==
                                        "" && (
                                        <tr>
                                            <th scope="row">
                                                GemstoneQuality1
                                            </th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .GemstoneQuality1.value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.GemstoneQuality2 &&
                                    getAllMetafields.GemstoneQuality2.value !==
                                        "" && (
                                        <tr>
                                            <th scope="row">
                                                GemstoneQuality2
                                            </th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .GemstoneQuality2.value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.GemstoneShape1 &&
                                    getAllMetafields.GemstoneShape1.value !==
                                        "" && (
                                        <tr>
                                            <th scope="row">GemstoneShape1</th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .GemstoneShape1.value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.GemstoneShape2 &&
                                    getAllMetafields.GemstoneShape2.value !==
                                        "" && (
                                        <tr>
                                            <th scope="row">GemstoneShape2</th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .GemstoneShape2.value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.GemstoneType1 &&
                                    getAllMetafields.GemstoneType1.value !==
                                        "" && (
                                        <tr>
                                            <th scope="row">GemstoneType1</th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .GemstoneType1.value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.GemstoneType2 &&
                                    getAllMetafields.GemstoneType2.value !==
                                        "" && (
                                        <tr>
                                            <th scope="row">GemstoneType2</th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .GemstoneType2.value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.MaximumCarat &&
                                    getAllMetafields.MaximumCarat.value !==
                                        "" && (
                                        <tr style={{ display: "none" }}>
                                            <th scope="row">MaximumCarat</th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .MaximumCarat.value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.MinimumCarat &&
                                    getAllMetafields.MinimumCarat.value !==
                                        "" && (
                                        <tr style={{ display: "none" }}>
                                            <th scope="row">MinimumCarat</th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .MinimumCarat.value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.NoOfGemstones1 &&
                                    getAllMetafields.NoOfGemstones1.value !==
                                        "" && (
                                        <tr>
                                            <th scope="row">NoOfGemstones1</th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .NoOfGemstones1.value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.NoOfGemstones2 &&
                                    getAllMetafields.NoOfGemstones2.value !==
                                        "" && (
                                        <tr>
                                            <th scope="row">NoOfGemstones2</th>
                                            <td>
                                                {
                                                    getAllMetafields
                                                        .NoOfGemstones2.value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.ringSize &&
                                    getAllMetafields.ringSize.value !== "" && (
                                        <tr>
                                            <th scope="row">RingSize</th>
                                            <td>
                                                {
                                                    getAllMetafields.ringSize
                                                        .value
                                                }
                                            </td>
                                        </tr>
                                    )}
                                {getAllMetafields.shape &&
                                    getAllMetafields.shape.value !== "" && (
                                        <tr>
                                            <th scope="row">Shape</th>
                                            <td>
                                                {getAllMetafields.shape.value}
                                            </td>
                                        </tr>
                                    )}
                            </tbody>
                        </table>
                    </div>
                </>
            );
        } else {
            return null;
        }
    } else {
        return null;
    }
};

export default ProductContainer;
