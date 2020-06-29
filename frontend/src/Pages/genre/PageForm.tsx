import * as React from 'react';
import {Page} from "../../components/Page";
import {Box, Fab} from "@material-ui/core";
import Form from './Form/Form'


const PageForm = () => {
    return (
        <Page title={"Cadastro gêneros"}>
            <Box>
            <Form/>
            </Box>
        </Page>

    )
}

export default PageForm;