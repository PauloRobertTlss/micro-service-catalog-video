import React from 'react';
import {makeStyles, Theme, createStyles} from '@material-ui/core/styles';
import Link, {LinkProps} from '@material-ui/core/Link';
import Typography from '@material-ui/core/Typography';
import UiBreadcrumbs from '@material-ui/core/Breadcrumbs';
import {Route} from 'react-router';
import {Link as RouterLink} from 'react-router-dom';
import Location from 'history';
import routes from '../routes';
import RouteParser from 'route-parser';
import {Box, Container} from "@material-ui/core";

const breadcrumbNameMap: { [key: string]: string } = {};

routes.forEach(route => breadcrumbNameMap[route.path as string] = route.label);

const useStyles = makeStyles((theme: Theme) =>
    createStyles({
        root: {
            display: 'flex',
            flexDirection: 'column',
        },
        linkRouter: {
            color: "#4db5ab",
            "&:focus, &:active": {
                color: "#4db5ab",
            },
            "&:hover": {
                color: "#055a52"
            },
            "li": {
                backgroundColor: "whiteOff",
                "&::after": {
                    content: '',
                    height: 16,
                    width: 16,
                    background: 'red no-repeat center center',
                    verticalAlign: 'middle'
                }
            }

        }

    }),
);

interface LinkRouterProps extends LinkProps {
    to: string;
    replace?: boolean;
}

const LinkRouter = (props: LinkRouterProps) => <Link {...props} component={RouterLink as any}/>;

export default function Breadcrumbs() {
    const classes = useStyles();

    function makeBreadcrumb(location: Location) {

        const pathnames = location.pathname.split('/').filter((x) => x);
        pathnames.unshift('/');
        return (
            <UiBreadcrumbs aria-label="breadcrumb">
                {
                    pathnames.map((value, index) => {
                        const last = index === pathnames.length - 1;
                        const to = `${pathnames.slice(0, index + 1).join('/').replace('//', '/')}`;

                        const routeMatch = Object
                            .keys(breadcrumbNameMap)
                            .find(path => new RouteParser(path).match(to))

                        if (routeMatch === undefined) {
                            return false;
                        }

                        return last ? (
                            <Typography color="textPrimary" key={to}>
                                {breadcrumbNameMap[routeMatch]}
                            </Typography>
                        ) : (
                            <LinkRouter color="inherit" to={to}
                                        key={to}
                                        className={classes.linkRouter}
                            >
                                {breadcrumbNameMap[routeMatch]}
                            </LinkRouter>
                        );
                    })
                }
            </UiBreadcrumbs>
        );
    }

    return (
        <Container>
            <Box paddingBottom={'9px'}>
            <Route>
                {
                    ({location}: { location: Location }) => makeBreadcrumb(location)
                }
            </Route>
            </Box>
        </Container>
    );
}