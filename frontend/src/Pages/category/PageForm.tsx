import * as React from 'react';
import {Page} from "../../components/Page";
import {Box, Fab} from "@material-ui/core";
import Form from './Form'
import {useParams} from "react-router";

const PageForm = () => {
    const {id} = useParams();

    return (
        <Page title={id ? "Cadastro categorias" : "Editar categoria"}>
            <Box>

            <Form/>
            </Box>
        </Page>

    )
}

export default PageForm;