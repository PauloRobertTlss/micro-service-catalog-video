import * as React from 'react';
import {Box, Button, Checkbox, FormControlLabel, RadioGroup, TextField, Radio, FormControl,FormLabel, Theme} from "@material-ui/core";
import {ButtonProps} from '@material-ui/core/Button'
import {makeStyles} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import castMemberHttp from "../../../utils/http/cast-member-http";
import {useEffect, useState} from "react";


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

    const {register, handleSubmit, getValues, setValue} = useForm();
    const [loading, setLoading] = useState<boolean>(false);

    useEffect(() => {
        register({name: "type"});
        return () => {} //willmount() unica vez
    },[register]);



    function onSubmit(formData, event) {

        setLoading(true);

        castMemberHttp
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
                variant={"outlined"}
                />
            <FormControl margin="normal">
                <FormLabel component="legend">Tipo</FormLabel>
            <RadioGroup
               name="type"
               onChange={(e) => {
                   setValue('type', parseInt(e.target.value));
               }}
            >
                <FormControlLabel value="1" control={<Radio color={'primary'}/>} label="Diretor"/>
                <FormControlLabel value="2" control={<Radio color={'primary'}/>} label="Ator"/>

            </RadioGroup>
            </FormControl>

            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)}>Salvar e continuar editando</Button>
                <Button {...buttonProps} type="submit">Salvar</Button>
            </Box>
        </form>
    )

};

export default Form;