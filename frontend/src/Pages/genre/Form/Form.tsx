import React from "react";
import {
    Box,
    Button,
    TextField,
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

const KEY_CATEGORIES:string = "categories_id";

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
        register({name: KEY_CATEGORIES})
    },[register]);

    useEffect(() => {
        categoryHttp
            .list()
            .then(({data}) => setCategories(data.data));
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
                variant={"outlined"}
                />

            <TextField
                select
                name="categories_id"
                value={watch(KEY_CATEGORIES)}
                label="Categorias"
                margin={"normal"}
                variant="outlined"
                fullWidth
                onChange={(e) => {
                    setValue(KEY_CATEGORIES, parseInt(e.target.value));
                }}
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