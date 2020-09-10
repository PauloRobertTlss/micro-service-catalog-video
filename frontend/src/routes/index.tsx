import {RouteProps} from "react-router-dom";
import Dashboard from "../Pages/Dashboard";
import CategoryList from "../Pages/category/PageList";
import CategoryCreate from "../Pages/category/PageForm";
import CastMemberList from "../Pages/cast-member/PageList";
import CastMemberCreate from "../Pages/cast-member/PageForm";
import GenreList from "../Pages/genre/PageList";
import GenreCreate from "../Pages/genre/PageForm";

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
        component: CategoryCreate,
        exact: true
    },
    {
        slug: 'categories.edit',
        label: 'Editar categoria',
        path: '/categories/:id/edit',
        component: CategoryCreate,
        exact: true
    },
    {
        slug: 'cast_members.list',
        label: 'Listar elenco',
        path: '/cast-members',
        component: CastMemberList,
        exact: true
    },
    {
        slug: 'cast_members.create',
        label: 'Registrar elenco',
        path: '/cast-members/create',
        component: CastMemberCreate,
        exact: true
    },
    {
        slug: 'cast_members.edit',
        label: 'Editar categoria',
        path: '/cast-members/:id/edit',
        component: CastMemberList,
        exact: true
    },
    {
        slug: 'genres.list',
        label: 'Listar gênero',
        path: '/genres',
        component: GenreList,
        exact: true
    },
    {
        slug: 'genres.create',
        label: 'Registrar gênero',
        path: '/genres/create',
        component: GenreCreate,
        exact: true
    },
    {
        slug: 'genres.edit',
        label: 'Editar categoria',
        path: '/genres/:id/edit',
        component: GenreList,
        exact: true
    },

];

export default routes;