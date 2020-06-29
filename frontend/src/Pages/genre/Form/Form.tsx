import * as React from 'react';
import {
    Box,
    Button,
    Checkbox,
    FormControlLabel,
    RadioGroup,
    TextField,
    Radio,
    FormControl,
    FormLabel,
    Theme,
    MenuItem
} from "@material-ui/core";
import {ButtonProps} from '@material-ui/core/Button'
import {useForm} from "react-hook-form";
import {useEffect, useState} from "react";
import categoryHttp from "../../../utils/http/category-http";
import genreHttp from "../../../utils/http/genre-http";
import {makeStyles} from "@material-ui/core/styles";


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

    const [categories, setCategories] = useState<any[]>([]);
    const {register, handleSubmit, getValues, setValue, watch} = useForm({
        defaultValues: {
            categories_id: []
        }
    });

    useEffect(() => {
        register({name: "categories_id"})
    },[register]);

    useEffect(() => {
        categoryHttp
            .list()
            .then(resp => setCategories(resp.data.data));
    }, []); //observar infomações não há limits


    function onSubmit(formData, event) {

        genreHttp
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
                select
                name="categories_id"
                value={watch('categories_id')}
                label="Categorias"
                margin={"normal"}
                variant="outlined"
                fullWidth
                // onChange={(e) => {
                //     setValue("categories_id", e.target.value)
                // }}
                SelectProps={{
                    multiple: true
                }}

            >
                <MenuItem value="" disabled>
                    <em>Selecionar categoria(s)</em>
                </MenuItem>
                {
                    categories.map(
                        (category, key) => (
                            <MenuItem key={key} value={category.id}>{category.name}</MenuItem>
                        )
                    )
                }
            </TextField>

            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)}>Salvar e continuar editando</Button>
                <Button {...buttonProps} type="submit">Salvar</Button>
            </Box>
        </form>
    )

};

export default Form;