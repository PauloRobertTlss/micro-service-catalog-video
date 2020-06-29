import * as React from 'react';
import {Box, Button, Checkbox, TextField, Theme} from "@material-ui/core";
import {ButtonProps} from '@material-ui/core/Button'
import {makeStyles} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import categoryHttp from "../../../utils/http/category-http";


const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

const Form = () => {
    const classes = useStyles();

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: "contained",
        color: 'secondary'
    };

    const {register, getValues, handleSubmit} = useForm({
        defaultValues: {
            is_active: true
        }
    });

    function onSubmit(formData, event) {

        categoryHttp
            .create(formData)
            .then((resp) => console.log('success'));

    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                inputRef={register}
                name="name"
                label="Nome"
                fullWidth
                />
            <TextField
                inputRef={register}
                name="description"
                label="descrição"
                multiline
                rows={4}
                fullWidth
                margin={"normal"}
            />
            <Checkbox
                inputRef={register}
                color={'primary'}
                name="is_active"
                defaultChecked
             />
            Ativo?
            <Box dir={"rtl"}>
                <Button
                    color={"primary"}
                    {...buttonProps}
                    onClick={() => onSubmit(getValues(), null)}>Salvar e continuar editando</Button>
                <Button
                    {...buttonProps}
                    type="submit">Salvar</Button>
            </Box>
        </form>
    )

};

export default Form;