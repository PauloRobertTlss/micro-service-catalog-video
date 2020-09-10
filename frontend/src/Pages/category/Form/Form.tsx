import * as React from 'react';
import {Box, Button, Checkbox, FormControlLabel, TextField, Theme} from "@material-ui/core";
import {ButtonProps} from '@material-ui/core/Button'
import {makeStyles} from "@material-ui/core/styles";
import {useForm} from "react-hook-form";
import categoryHttp from "../../../utils/http/category-http";
import * as yup from '../../../utils/vendor/yup';
import {useEffect, useState} from "react";
import {useParams} from "react-router";


const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

const validationSchema = yup.object().shape({
    name: yup.string()
        .label('Nome')
        .required()
});

const Form = () => {
    const classes = useStyles();

    const {register, handleSubmit, getValues, setValue, errors, reset, watch} = useForm({
        validationSchema
    });

    const {id} = useParams();
    const [category, setCategory] = useState<{ id: string } | null>(null);
    const [loading, setLoading] = useState<boolean>(false);

    const buttonProps: ButtonProps = {
        disabled: loading,
        className: classes.submit,
        variant: "contained",
        color: 'secondary'
    };

    useEffect(() => {
        register({name: 'is_active'})
    }, [register]);

    useEffect(() => {

        if (!id) {
            return;
        }

        setLoading(true);

        categoryHttp.get(id)
            .then(({data}) => {
                setCategory(data.data);
                reset(data.data);
            }).finally(() => setLoading(false))
    });

    function onSubmit(formData, event) {

        setLoading(true);

        const http = !category
            ? categoryHttp.create(formData)
            : categoryHttp.update(category.id, formData);

        http.finally(() => setLoading(false))


    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                inputRef={register}
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
                error={errors.name !== undefined}
                helperText={errors.name && errors.name.message}
                InputLabelProps={{shrink: true}}
                disabled={loading}
            />
            <TextField
                inputRef={register}
                name="description"
                label="descrição"
                multiline
                rows={4}
                fullWidth
                margin={"normal"}
                disabled={loading}
            />
            <FormControlLabel
                disabled={loading}
                control={
                    <Checkbox
                        color={'primary'}
                        name="is_active"
                        onChange={() => setValue('is_active', !getValues()['is_active'])}
                        checked={watch('is_active')}
                    />}
                label={'Ativo?'}
                labelPlacement={'end'}
            />

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