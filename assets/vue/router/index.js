import { createRouter, createWebHistory } from 'vue-router';
import courseRoutes from './course';
import accountRoutes from './account';

//import courseCategoryRoutes from './coursecategory';
import documents from './documents';
import store from '../store';
import Login from '../views/Login.vue';
//import Legacy from '../views/Legacy.vue';
//import Home from '../views/Home.vue';
import MyCourseList from '../views/user/courses/List.vue';
import MySessionList from '../views/user/sessions/List.vue';

import CatalogLayout from '../layouts/Catalog.vue';
import MyCoursesLayout from '../layouts/MyCourses.vue';

import CourseCatalog from '../views/course/Catalog.vue';
import SessionCatalog from '../views/course/CatalogSession.vue';
import CourseHome from '../views/course/Home.vue';

import Index from '../pages/Index.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {path: '/', name: 'Home', component: Index},
        {
            path: '/login',
            name: 'Login',
            component: Login,
            meta: {
                layout: 'Empty'
            }
        },
        {
            path: '/course/:id/home', name: 'CourseHome', component: CourseHome
        },
        {
            path: '/courses',
            component: MyCoursesLayout,
            children: [
                {
                    path: '/courses', name: 'MyCourses', component: MyCourseList,
                    meta: {requiresAuth: true},
                },
                {
                    path: '/sessions', name: 'MySessions', component: MySessionList,
                    meta: {requiresAuth: true},
                },
            ],
        },
        {
            path: '/catalog',
            redirect: '/catalog/course',
            name: 'Catalog',
            component: CatalogLayout,
            children: [
                {
                    path: 'course',
                    component: CourseCatalog
                },
                {
                    path: 'session',
                    component: SessionCatalog
                },
            ],
            meta: {requiresAuth: true},
        },
        courseRoutes,
        //courseCategoryRoutes,
        documents,
        accountRoutes
    ]
});

router.beforeEach((to, from, next) => {
    console.log('beforeEach');
    if (to.matched.some(record => record.meta.requiresAuth)) {
        console.log('requiresAuth');
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (store.getters['security/isAuthenticated']) {
            next();
        } else {
            next({
                path: '/login',
                query: {redirect: to.fullPath},
            });
        }
    } else {
        console.log('next');
        next(); // make sure to always call next()!
    }
});

export default router;
