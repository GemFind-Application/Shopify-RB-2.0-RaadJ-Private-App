import React from "react";
import { Navigate, Route } from "react-router-dom";
import AppFrame from "./AppFrame";

function App() {
    return (
        <div>
            <Switch>
                <Navigate exact from="/home" to="/home/about" />
                <Route
                    exact
                    path="/home/:page?"
                    render={(props) => <AppFrame {...props} />}
                />
            </Switch>
        </div>
    );
}

export default App;
