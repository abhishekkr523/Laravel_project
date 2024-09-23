<template>
    <div class="container">
        <div class="nav-links">
            <div class="nav-items">
                <span><router-link to="/">Products List</router-link></span>
                <span><router-link to="/add-product">Add Product</router-link></span>
                <span><router-link to="/products">Products DataTables</router-link></span>
            </div>
            <div>
                <h3 class="logo"><router-link to="/">YouCan</router-link></h3>
            </div>
        </div>
        <div class="main">
            <router-view></router-view>
        </div>
    </div>
</template>


<script>
import home from "./components/home.vue";
import AddProduct from "./components/AddProduct.vue";
import ProductsList from "./components/ProductsList.vue";
import products from "./components/products.vue";
import paginationTesting from "./components/paginationTesting.vue";

export default {
    name: "App",
    components: {
        home,
        AddProduct,
        ProductsList,
        products,
        paginationTesting,
    },
    methods: {
        exportData() {
            // Call the API route to export products
            window.location.href = `${window.location.origin}/api/export-products`;
        },
    },
};
</script>

<style scoped>
/* Container for the entire page */
.container {
    max-width: 1400px;
    margin: 10px auto;
    padding: 20px;
    background: #e0e0e0; /* Light grey background */
    border-radius: 12px;
    box-shadow: 8px 8px 16px #bcbcbc, -8px -8px 16px #ffffff; /* Neumorphism shadow */
}

/* Navigation links */
.nav-links {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #e0e0e0; /* Light grey background */
    border-radius: 12px;
    box-shadow: 4px 4px 8px #f0d7da, -4px -4px 8px #FFFFFF;
    margin-bottom: 20px;
}

/* Logo styling */
.logo {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    text-decoration: none;
}
.logo a {
    color: #f0a;
    text-decoration: none;
    letter-spacing: 1px;
    font-size: 22px;
}
/* Navigation links */
.nav-links span {
    margin: 5px;
    font-size: 16px;
    color: #333;
}

.nav-links span a {
    text-decoration: none;
    color: #333;
    padding: 10px 15px;
    border-radius: 8px;
    box-shadow: 4px 4px 8px #bcbcbc, -4px -4px 8px #ffffff; /* Neumorphism shadow */
    transition: background 0.3s, color 0.3s;
}

.nav-links span a:hover {
    background: #d0d0d0;
    color: #000;
}

/* Main content area */

.mai {
    margin-top: 20px;
}
</style>
