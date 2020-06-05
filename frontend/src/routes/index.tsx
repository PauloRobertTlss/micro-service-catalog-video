import {RouteProps} from "react-router-dom";
import Dashboard from "../Pages/Dashboard";
import CategoryList from "../Pages/category/List";

export interface CustomRouteProps extends RouteProps {
    slug:  string,
    label: string
}

const routes: CustomRouteProps[] = [
    {
        slug: 'dashboard',
        label: 'Dashboard',
        path: '/',
        component: Dashboard,
        exact: true
    },
    {
        slug: 'categories.list',
        label: 'Listar categorias',
        path: '/categories',
        component: CategoryList,
        exact: true
    },
    {
        slug: 'categories.create',
        label: 'Registrar categoria',
        path: '/categories/create',
        component: CategoryList,
        exact: true
    },
    {
        slug: 'categories.edit',
        label: 'Editar categoria',
        path: '/categories/:id/edit',
        component: CategoryList,
        exact: true
    },

];

export default routes;