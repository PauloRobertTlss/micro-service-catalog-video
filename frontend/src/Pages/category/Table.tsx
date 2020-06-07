import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import {useEffect, useState} from "react";
import {Chip} from "@material-ui/core";
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import categoryHttp from "../../utils/http/category-http";

type Props = {

}


const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: 'is_active',
        label: 'Ativa',
        options:{
            customBodyRender(value, tableMeta, updateValue){
                return (
                        value ? <Chip label="sim" color="primary"/> : <Chip label="não" color="secondary"/>

                )
            }
        }
    },
    {
        name: 'name',
        label: 'Nome',
    },
    {
        name: 'description',
        label: 'Descrição'
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

type Category = {
    name: string
}

interface CategoryInterface {
    name: string,
    categories: Category[]
}


const Table = (props: Props) => {

    const [count, setCount] = useState(0);
    const [data, setData] = useState<CategoryInterface[]>([]);

    //compoentDidUpdate - listene
    useEffect(() => {
        console.log(count)
        categoryHttp
            .list<{data: CategoryInterface[]}>()
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
            title={'listagem de categorias'}
            data={data} />

    )

};

export default Table;