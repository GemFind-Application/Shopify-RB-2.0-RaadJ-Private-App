import { Card, DataTable, Scrollable } from "@shopify/polaris";
import React from "react";

const FailedImports = ({ importRows }) => {
    if (importRows.length > 0) {
        return (
            <div>
                <Scrollable shadow style={{ height: "300px" }} focusable>
                    <Card.Subsection>
                        <DataTable
                            columnContentTypes={["text", "numeric", "numeric"]}
                            headings={["Time", "Product", "Error"]}
                            rows={importRows}
                        />
                    </Card.Subsection>
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

export default FailedImports;
