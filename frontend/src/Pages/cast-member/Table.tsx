import * as React from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables";
import {useEffect, useState} from "react";
import {httpVideo} from "../../utils/http";
import {Chip} from "@material-ui/core";
import format from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import castMemberHttp from "../../utils/http/cast-member-http";

type Props = {

}

const CastMemberTypeMap: { [key: string]: string } = {1: 'Diretor', 2: 'Ator'};


const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: 'name',
        label: 'Nome',
    },
    {
        name: 'type',
        label: 'Tipo',
        options:{
            customBodyRender: (value: number, tableMeta, updateValue) => {

                return (
                    value == 1 ? <Chip label={CastMemberTypeMap[value]} color="primary"/> : <Chip label={CastMemberTypeMap[value]} color="secondary"/>
                )
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


const Table = (props: Props) => {

    const [count, setCount] = useState(0);
    const [data, setData] = useState([]);

    //compoentDidUpdate - listene
    useEffect(() => {
        castMemberHttp
            .list()
            .then(resp => setData(resp.data.data));
        return () => {} //willmount() unica vez
    }, [count]); //observar infomações não há limits

    //compoentDidUpdate - listene
    // useEffect(() => {
    //
    // }, [count]); //observar infomações não há limits

    return (

        <MUIDataTable
            columns={columnsDefinition}
            title={'listagem de membros de elenco'}
            data={data} />

    )

};

export default Table;