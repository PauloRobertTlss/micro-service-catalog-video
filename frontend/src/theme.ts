import {createMuiTheme, SimplePaletteColorOptions} from "@material-ui/core";
import {PaletteOptions} from "@material-ui/core/styles/createPalette";
import {green} from "@material-ui/core/colors";

const palette: PaletteOptions = {
    primary: {
        main: '#79aec8',
        contrastText: '#fff'
    },
    secondary: {
        main: '#4db5ab',
        contrastText: '#fff',
        dark: '#055a52'
    },
    background: {
        default: '#fff'
    },
    success: {
        main: green["500"],

    }
}

const theme = createMuiTheme({
    palette,
    overrides: {
        MuiFormLabel: {
            root: {
                fontSize: '1.8rem',
            }
        },
        MuiInputBase: {
            input: {
                fontSize: '1rem',
                color: 'primary'
                //atingi tudo
            }
        },
        MUIDataTable: {
            paper: {
                boxShadow: 'none'
            },
        },
        MUIDataTableToolbar: {
            root: {
                minHeight: '36px',
                backgroundColor: palette!.background!.default //no num assertion
            },
            icon: {
                color: (palette!.primary as SimplePaletteColorOptions).main,
                '&:hover, &:active, &:focus': {
                    color: '#055a52'
                }
            },
            iconActive: {
                color: '#055a52',
                '&:hover, &:active, &:focus': {
                    color: '#055a52'
                }

            }
        },
        MUIDataTableHeadCell: {
            fixedHeaderCommon: {
                paddingTop: 6,
                paddingBottom: 6,
                backgroundColor: (palette!.primary as SimplePaletteColorOptions).main,
                color: '#ffffff',
                '&:[aria-sort]': {
                    backgroundColor: '#459ac4'
                }

            },
            sortActive: {
                color: '#fff'
            },
            sortAction: {
                alignItems: 'center'
            },
            sortLabelRoot: {
                '& svg': {
                    color: '#fff!important'
                }
            }
        },
        MUIDataTableSelectCell: {
            headerCell: {
                backgroundColor: (palette!.primary as SimplePaletteColorOptions).main,
                '& span': {
                    color: '#fff !important'
                }
            }
        },
        MUIDataTableBodyCell: {
            root: {
                color: (palette!.secondary as SimplePaletteColorOptions).main,
                '&:hover, &:active, &:focus': {
                    color: (palette!.secondary as SimplePaletteColorOptions).main
                }
            }

        },
        MUIDataTableToolbarSelect: {
            title: {
                color: (palette!.secondary as SimplePaletteColorOptions).main
            },
            iconButton: {
                color: (palette!.secondary as SimplePaletteColorOptions).main

            }
        },
        MUIDataTableBodyRow: {
            root: {
                '&:nth-child(odd)': {
                    backgroundColor: palette!.background!.default
                }
            }
        },
        MUIDataTablePagination: {
            root: {
                color: (palette!.secondary as SimplePaletteColorOptions).main
            }
        }

    }
});

export default theme;