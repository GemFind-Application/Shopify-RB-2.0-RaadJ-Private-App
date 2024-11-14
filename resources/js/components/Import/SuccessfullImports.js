import { Card, DataTable, Scrollable } from "@shopify/polaris";
import React from "react";

const SuccessfullImports = ({ importRows }) => {
    if (importRows.length > 0) {
        return (
            <div>
                <Scrollable shadow style={{ height: "300px" }} focusable>
                    <Card>
                        <Card.Section>
                            <DataTable
                                columnContentTypes={[
                                    "text",
                                    "numeric",
                                    "numeric",
                                ]}
                                headings={["Product", "Product ID", "Time"]}
                                rows={importRows}
                            />
                        </Card.Section>
                    </Card>
                </Scrollable>
            </div>
        );
    } else {
        return (
            <div>
                <Scrollable shadow style={{ height: "300px" }} focusable>
                    <Card>
                        <Card.Section>No Data Found</Card.Section>
                    </Card>
                </Scrollable>
            </div>
        );
    }
};

export default SuccessfullImports;
