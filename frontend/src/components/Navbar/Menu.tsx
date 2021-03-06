import React,{useState} from 'react';
import {IconButton, Menu as UiMenu, MenuItem} from '@material-ui/core';
import MenuIcon from '@material-ui/icons/Menu'
import routes, {CustomRouteProps} from "../../routes";
import {Link} from "react-router-dom";

const allowedRoutes = {
    'dashboard': 'Dashboard',
    'categories.list' : 'Categorias',
    'cast_members.list' : "Membros de elenco",
    'genres.list' : "Gêneros",
};

const menuRoutes = routes.filter(route => Object.keys(allowedRoutes).includes(route.slug));

export const Menu: React.FC = () => {

    const [anchorEl, setAnchorEl] = useState(null);
    const open = Boolean(anchorEl);
    const handleOpen = (event: any) => setAnchorEl(event.currentTarget);
    const handleClose = () => setAnchorEl(null);

    return (
        <React.Fragment>
            <IconButton
                edge="start"
                color="inherit"
                aria-label="open drawer"
                aria-controls="menu-appbar"
                aria-haspopup="true"
                onClick={handleOpen}
            >
                <MenuIcon/>
            </IconButton>

            <UiMenu
                id="menu-appbar"
                open={open}
                anchorEl={anchorEl}
                onClose={handleClose}
                anchorOrigin={{vertical: 'bottom', horizontal: 'center'}}
                transformOrigin={{vertical: 'top', horizontal: 'center'}}
                getContentAnchorEl={null}
            >
                {
                    Object.keys(allowedRoutes).map((routeName, key) => {
                      const route = menuRoutes.find(route => route.slug === routeName) as CustomRouteProps;
                      return (
                          <MenuItem key={key} component={Link} to={route.path as string} onClick={handleClose}>
                              {allowedRoutes[routeName]}
                          </MenuItem>
                      )
                    })
                }

            </UiMenu>
        </React.Fragment>
    )

}