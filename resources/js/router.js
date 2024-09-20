import {createWebHistory, createRouter} from 'vue-router';
import ProductsList from './components/ProductsList.vue';
import AddProduct from './components/AddProduct.vue';
import UpdateProduct from './components/UpdateProduct.vue';
import products from './components/products.vue';
import paginationTesting from './components/paginationTesting.vue';

export const routes = [
    {
        name: 'ProductsList',
        path: '/',
        component: ProductsList
    },
    {
        name: 'AddProduct',
        path: '/add-product',
        component: AddProduct
    },
    {
        name: 'UpdateProduct',  
        path: '/update-product/:id',
        component: UpdateProduct
    },
    {
        name: 'products',  
        path: '/products',
        component: products
    },
    {
        name: 'paginationTesting',  
        path: '/paginationTesting',
        component: paginationTesting
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes
});

export default router;
