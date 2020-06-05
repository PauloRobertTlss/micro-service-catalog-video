import React from 'react';
import './App.css';
import {Navbar} from "./components/Navbar";
import {Box} from "@material-ui/core";
import {BrowserRouter} from "react-router-dom";
import AppRouter from "./routes/AppRouter";
import Breadcrumbs from "./components/BreadCrumbs";

const App: React.FC = () => {

    return (
        <React.Fragment>
            <BrowserRouter>
            <Navbar/>

              <Box paddingTop={'69px'}>
                  <Breadcrumbs/>
                <AppRouter/>
              </Box>
            </BrowserRouter>
        </React.Fragment>
    );
}

export default App;
