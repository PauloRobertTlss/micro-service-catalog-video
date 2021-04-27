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
import categoryHttp from "../../utils/http/category-http";
import genreHttp from "../../utils/http/genre-http";
import {makeStyles} from "@material-ui/core/styles";
import * as yup from "../../utils/vendor/yup";
import {useHistory, useParams} from "react-router";
import {Category, Genre} from "../../utils/models";
import {useSnackbar} from "notistack";


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
        .max(255),
    categories_id: yup.array()
        .label('Categorias')
        .required()
})

const KEY_CATEGORIES:string = "categories_id";

const Form = () => {
    const classes = useStyles();
    const snackBar = useSnackbar();
    const history = useHistory();

    const {
        register,
        handleSubmit,
        getValues,
        setValue,
        errors,
        reset,
        watch,
        triggerValidation
    } = useForm({
        validationSchema,
        defaultValues: {
            categories_id: [],
            name: null
        },
    });

    const {id} = useParams();
    const [genre, setGenre] = useState<Genre | null>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [categories, setCategories] = useState<Category[]>([]);

    const buttonProps: ButtonProps = {
        className: classes.submit,
        color: 'secondary',
        variant: "contained",
        disabled: loading
    };

    // useEffect(() => {
    //     register({name: KEY_CATEGORIES})
    // },[register]);

    useEffect(() => {
        let isSubscribed = true;
        (async () => {
            setLoading(true);
            const promises = [categoryHttp.list()];

            if (id) {
                promises.push(genreHttp.get(id));
            }

            try {
                const [categoriesResponse, genreResponse] = await Promise.all(promises);

                if (isSubscribed) {

                    setCategories(categoriesResponse.data.data);
                    if (id) {

                        setGenre(genreResponse.data.data);
                        const categories = genreResponse.data.data.categories_id.map(category => category.id);
                        reset({
                            ...genreResponse.data.data,
                            categories_id: categories
                        });
                    }
                }
            }catch (error) {
                snackBar.enqueueSnackbar('Server error', {
                    variant: "error"
                });
            } finally {
                setLoading(false)
            }
        })();

        return () => {
           isSubscribed = false;
        }

    }, []); //observar infomações não há limits

    useEffect(() => {
        register({name: KEY_CATEGORIES})
    }, [register]);

    async function onSubmit(formData, event) {

        setLoading(true);
        try {
            const http = !genre
                ? genreHttp.create({})
                : genreHttp.update(genre.id, formData);
            const {data} = await http;
            snackBar.enqueueSnackbar(
                'Membro de elenco salvo com sucesso',
                {variant: 'success'}
            );
            setTimeout(() => {
                event
                    ? (
                        id
                            ? history.replace(`/genres/${data.data.id}/edit`)
                            : history.push(`/genres/${data.data.id}/edit`)
                    )
                    : history.push('/genres')
            });
        } catch (error) {
            console.error(error);
            snackBar.enqueueSnackbar(
                'Não foi possível salvar gênero',
                {variant: 'error'}
            )
        } finally {
            setLoading(false)
        }
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
                disabled={loading}
                />

            <TextField
                select
                name="categories_id"
                value={watch('categories_id')}
                label="Categorias"
                margin={'normal'}
                variant={'outlined'}
                fullWidth
                onChange={(e) => {
                    setValue(KEY_CATEGORIES, e.target.value);
                }}
                SelectProps={{
                    multiple: true
                }}
                disabled={loading}
                error={errors.categories_id !== undefined}
                helperText={errors.categories_id && errors[KEY_CATEGORIES].message}
                InputLabelProps={{shrink: true}}
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