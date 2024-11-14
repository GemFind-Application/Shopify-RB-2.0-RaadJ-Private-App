import React, { useCallback, useState } from "react";
import { CalloutCard, Card, List } from "@shopify/polaris";

function GettingStarted() {
    return (
        <CalloutCard
            title="Next Steps for Getting Started with GemFind RingBuilder
            "
            illustration="https://cdn.shopify.com/s/assets/admin/checkout/settings-customizecart-705f57c725ac05be5a34ec20c05b94298cb8afd10aac7bd9c7ad02030f48cfa0.svg"
            primaryAction={{
                content:
                    "Got questions? Contact us at sales@gemfind.com or 800-373-4373",
                url: "#",
            }}
        >
            <Card.Section>
                <List>
                    <List.Item>
                        Your RingBuilder app requires a Jewelcloud account with
                        GemFind.
                    </List.Item>
                    <List.Item>
                        Please click the Settings tab and fill out the
                        application so we can create your Jewelcloud account.
                    </List.Item>
                    <List.Item>
                        Once your Jewelcloud account has been activated our
                        support team will email your Jewelcloud account
                        information and instructions for selecting your diamond
                        vendors and setting your markups.
                    </List.Item>
                </List>
            </Card.Section>
        </CalloutCard>
    );
}

export default GettingStarted;
