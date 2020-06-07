import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import {useEffect, useState} from "react";
import {httpVideo} from "../../utils/http";
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import genreHttp from "../../utils/http/genre-http";

type Props = {

}

export interface GenreProps {
    name: string
}

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: 'name',
        label: 'Nome',
    },
    {
        name: 'categories',
        label: 'Categorias',
        options:{
            customBodyRender: (value: GenreProps[], tableMeta, updateValue) => {
                return value.map(value => value.name).join('- ');
            }
        }
    },
    {
        name: 'created_at',
        label: 'Criado em',
        options:{
            customBodyRender(value, tableMeta, updateValue) {
                return (
                    <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>

                )
            }
        }
    }
];


interface GenreInterface {
    name: string,
    categories: GenreProps[],
    created: string
}

const Table = (props: Props) => {

    const [count, setCount] = useState(0);
    const [data, setData] = useState<GenreInterface[]>([]);

    //compoentDidUpdate - listene
    useEffect(() => {
        genreHttp
            .list<{data: GenreInterface[]}>()
            .then(resp => setData(resp.data.data));
        return () => {} //willmount() unica vez
    }, [count]); //observar infomações não há limits

    //compoentDidUpdate - listene
    useEffect(() => {
        console.log(count)
    }, [count]); //observar infomações não há limits

    return (
        <MUIDataTable
            columns={columnsDefinition}
            title={'listagem de gêneros'}
            data={data} />

    )

};

export default Table;