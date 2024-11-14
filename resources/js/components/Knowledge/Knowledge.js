import React, { useCallback, useState } from "react";
import { styled } from "@mui/material/styles";
import ArrowForwardIosSharpIcon from "@mui/icons-material/ArrowForwardIosSharp";
import MuiAccordion from "@mui/material/Accordion";
import MuiAccordionSummary from "@mui/material/AccordionSummary";
import MuiAccordionDetails from "@mui/material/AccordionDetails";
import {
    Banner,
    Card,
    DisplayText,
    Link,
    Stack,
    TextContainer,
} from "@shopify/polaris";



const Accordion = styled((props) => (
    <MuiAccordion disableGutters elevation={0} square {...props} />
))(({ theme }) => ({
    border: `1px solid ${theme.palette.divider}`,
    "&:not(:last-child)": {
        borderBottom: 0,
    },
    "&:before": {
        display: "none",
    },
}));

const AccordionSummary = styled((props) => (
    <MuiAccordionSummary
        expandIcon={<ArrowForwardIosSharpIcon sx={{ fontSize: "0.9rem" }} />}
        {...props}
    />
))(({ theme }) => ({
    backgroundColor:
        theme.palette.mode === "dark"
            ? "rgba(255, 255, 255, .05)"
            : "rgba(0, 0, 0, .03)",
    flexDirection: "row-reverse",
    "& .MuiAccordionSummary-expandIconWrapper.Mui-expanded": {
        transform: "rotate(90deg)",
    },
    "& .MuiAccordionSummary-content": {
        marginLeft: theme.spacing(1),
    },
}));

const AccordionDetails = styled(MuiAccordionDetails)(({ theme }) => ({
    padding: theme.spacing(2),
    borderTop: "1px solid rgba(0, 0, 0, .125)",
}));

function Knowledge() {
    const [expanded, setExpanded] = React.useState("panel1");

    const handleChange = (panel) => (event, newExpanded) => {
        setExpanded(newExpanded ? panel : false);
    };

    return (
        <div style={{ height: "200px" }}>
            <Card sectioned>
                <Stack vertical>
                    <DisplayText size="large">Help Center</DisplayText>
                    <Banner
                        title="We’d love to hear from you"
                        action={{
                            content: "Free Consultation",
                            url: "https://gemfind.com/free-consultation/",
                        }}
                        status="info"
                    >
                        <p>
                            Need help? Schedule a Free Consultation by clicking
                            below link
                        </p>
                    </Banner>
                    <TextContainer />
                    <DisplayText size="large">Knowledge Base</DisplayText>
                    <Accordion
                        expanded={expanded === "panel1"}
                        onChange={handleChange("panel1")}
                    >
                        <AccordionSummary
                            aria-controls="panel1d-content"
                            id="panel1d-header"
                        >
                            <TextContainer>What is JewelCloud?</TextContainer>
                        </AccordionSummary>
                        <AccordionDetails>
                            <TextContainer>
                                JewelCloud® is an app that allows you to
                                showcase products from suppliers and offer your
                                customers an improved shopping experience.
                            </TextContainer>
                        </AccordionDetails>
                    </Accordion>
                    <Accordion
                        expanded={expanded === "panel2"}
                        onChange={handleChange("panel2")}
                    >
                        <AccordionSummary
                            aria-controls="panel2d-content"
                            id="panel2d-header"
                        >
                            <TextContainer>
                                What is the use of RingBuilder?
                            </TextContainer>
                        </AccordionSummary>
                        <AccordionDetails>
                            <TextContainer>
                                RingBuilder ® is a retail ring and diamond
                                sourcing app that allows retailers to curate a
                                list of ring and diamond suppliers for online
                                shopping on their websites.
                            </TextContainer>
                        </AccordionDetails>
                    </Accordion>
                    <Accordion
                        expanded={expanded === "panel3"}
                        onChange={handleChange("panel3")}
                    >
                        <AccordionSummary
                            aria-controls="panel3d-content"
                            id="panel3d-header"
                        >
                            <TextContainer>
                                Where to get the DealerID?
                            </TextContainer>
                        </AccordionSummary>
                        <AccordionDetails>
                            <TextContainer>
                                JewelCloud membership need to take to subscribe
                                for RingBuilder. Click link below to access and
                                get more details about your Dealer ID. Dealer ID
                                is required to communicate with JC and display
                                data in store frontend.
                            </TextContainer>
                        </AccordionDetails>
                    </Accordion>
                    <Accordion
                        expanded={expanded === "panel4"}
                        onChange={handleChange("panel4")}
                    >
                        <AccordionSummary
                            aria-controls="panel3d-content"
                            id="panel4d-header"
                        >
                            <TextContainer>How to use the app?</TextContainer>
                        </AccordionSummary>
                        <AccordionDetails>
                            <TextContainer>
                                After installing the app, you will have to enter
                                DealerID in Setting tab. You can adjust other
                                settings according to your preference. You have
                                to subscribe to JewelCloud for get the DealerID
                                and it will be used in this app to function.
                            </TextContainer>
                        </AccordionDetails>
                    </Accordion>
                </Stack>
            </Card>
        </div>
    );
}

export default Knowledge;
