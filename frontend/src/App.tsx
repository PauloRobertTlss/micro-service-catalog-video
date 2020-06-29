import React from 'react';
import './App.css';
import {Navbar} from "./components/Navbar";
import {Box, CssBaseline, MuiThemeProvider} from "@material-ui/core";
import {BrowserRouter} from "react-router-dom";
import AppRouter from "./routes/AppRouter";
import Breadcrumbs from "./components/BreadCrumbs";
import theme from "./theme";

const App: React.FC = () => {

    return (
        <React.Fragment>
            <MuiThemeProvider theme={theme}>
                <CssBaseline/>
                <BrowserRouter>
                    <Navbar/>

                    <Box paddingTop={'69px'}>
                        <Breadcrumbs/>
                        <AppRouter/>
                    </Box>
                </BrowserRouter>
            </MuiThemeProvider>
        </React.Fragment>
    );
}

export default App;
