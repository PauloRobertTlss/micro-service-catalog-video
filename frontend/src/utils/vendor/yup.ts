import {LocaleObject, setLocale} from 'yup';

const ptBR: LocaleObject = {
    mixed: {
      required: '${path} é requerido'
    },
    string: {
        max: '${path} limite máximo ${max}',
        length: '${path} tamanho ${length}'
    },
    number: {
        max: '${path} limite máximo ${max}',
        min: '${path} minimo ${min}'
    }
}

setLocale(ptBR);
export * from 'yup';