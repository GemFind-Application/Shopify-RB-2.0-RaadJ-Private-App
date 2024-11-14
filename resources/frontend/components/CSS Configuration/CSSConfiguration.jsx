import React, { useCallback, useEffect, useState } from "react";
import {
    Button,
    Card,
    Layout,
    Frame,
    Toast,
    Banner,
    Link,
    List,
    Subheading,
    Heading,
    DataTable,
    Scrollable,
    Grid,
    ResourceList,
    Thumbnail,
    TextStyle,
    Tabs,
    SkeletonPage,
    SkeletonBodyText,
    TextContainer,
    SkeletonDisplayText,
} from "@shopify/polaris";
import axios from "axios";
import { SketchPicker } from "react-color";
import styles from "./pickerCss.module.css";

function CSSConfiguration() {
    const [loading, setLoading] = useState(false);
    const [showTable, setShowTable] = useState(false);
    const [linkColour, setLinkColour] = useState("#000000");
    const [hoverColour, setHoverColour] = useState("#000000");
    const [buttonColour, setButtonColour] = useState("#000000");
    const [sliderColour, setSliderColour] = useState("#000000");
    const [headerColour, setHeaderColour] = useState("#000000");
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

    //GET LOGS API
    useEffect(() => {
        const getCSSConfiguration = async () => {
            const res = await fetch(
                "/api/getCSSConfiguration/" +
                    document.getElementById("shopOrigin").value,
                {
                    method: "GET",
                }
            );
            const css_config = await res.json();
            setButtonColour(css_config.data.button);
            setHeaderColour(css_config.data.header);
            setHoverColour(css_config.data.hover);
            setLinkColour(css_config.data.link);
            setSliderColour(css_config.data.slider);
            setShowTable(true);
        };
        getCSSConfiguration();
    }, []);

    let handleCSSSettings = async (e) => {
        try {
            let payLoad = {
                shop_domain: document.getElementById("shopOrigin").value,
                linkColour: linkColour ? linkColour : '#000000',
                hoverColour: hoverColour ? hoverColour : '#000000',
                sliderColour: sliderColour ? sliderColour : '#000000',
                buttonColour: buttonColour ? buttonColour : '#000000',
                headerColour: headerColour ? headerColour : '#000000',
            };
            setLoading(true);
            let response = await axios.post("/api/saveCSSConfiguration", {
                data: payLoad,
            });
            console.log(response.data.status);
            if (response.data.status === true) {
                setToastContent(response.data.message);
                toggleActive();
            } else {
                setToastContent1(response.data.message);
                toggleActive1();
            }
            setLoading(false);
        } catch (err) {
            console.log(err);
        }
    };
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
    } else {
        return (
            <div style={{ height: "200px" }}>
                <Frame>
                    <Layout>
                        <Layout.Section>
                            <Card title="Dynamic CSS Configuration">
                                <Card.Section>
                                    <div className={styles.color_picker}>
                                        <div className={styles.color_picker_p1}>
                                            <p>Link color</p>
                                            <SketchPicker
                                                onChange={(e) => {
                                                    setLinkColour(e.hex);
                                                }}
                                                color={linkColour}
                                            />
                                        </div>
                                        <div className={styles.color_picker_p2}>
                                            <p>Hover Effect</p>
                                            <SketchPicker
                                                onChange={(e) => {
                                                    setHoverColour(e.hex);
                                                }}
                                                color={hoverColour}
                                            />
                                        </div>
                                        <div className={styles.color_picker}>
                                            <div
                                                className={
                                                    styles.color_picker_p2
                                                }
                                            >
                                                <p>Column Header Accent</p>
                                                <SketchPicker
                                                    onChange={(e) => {
                                                        setHeaderColour(e.hex);
                                                    }}
                                                    color={headerColour}
                                                />
                                            </div>
                                            <div
                                                className={
                                                    styles.color_picker_p2
                                                }
                                            >
                                                <p>Call To Action Button</p>
                                                <SketchPicker
                                                    onChange={(e) => {
                                                        setButtonColour(e.hex);
                                                    }}
                                                    color={buttonColour}
                                                />
                                            </div>
                                            <div
                                                className={
                                                    styles.color_picker_p2
                                                }
                                            >
                                                <p>Slider Effect</p>
                                                <SketchPicker
                                                    onChange={(e) => {
                                                        setSliderColour(e.hex);
                                                    }}
                                                    color={sliderColour}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </Card.Section>
                                <Card.Section>
                                    {/* <div
                                    className="lm_quote_save_button"
                                    style={{ float: "right" }}
                                > */}
                                    <Button
                                        primary
                                        loading={loading}
                                        onClick={handleCSSSettings}
                                    >
                                        Save
                                    </Button>
                                    {toastMarkup}
                                    {toastMarkup1}
                                    {/* </div> */}
                                </Card.Section>
                            </Card>
                        </Layout.Section>
                    </Layout>
                </Frame>
            </div>
        );
    }
}

export default CSSConfiguration;
