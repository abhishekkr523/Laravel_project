<template>
    <div class="update-product">
        <h2 class="component-title">Product Details</h2>
        <div class="product-details" style="    display: flex;
    align-items: center;
    justify-content: space-between;">
            <div class="product-details-left ">
            <div class="detail-group">
                <label for="name">Product Name: </label>
                <span class="product-detail">{{ product.name }}</span>
            </div>

            <div class="detail-group">
                <label for="price">Product Actual Price: </label>
                <span class="product-detail">{{
                    (product.price / (1 - product.discount / 100)).toFixed(2)
                }}</span>
            </div>
            <div class="detail-group">
                <label for="price"> Discount Price: </label>
                <span class="product-detail">{{ product.price }}</span>
            </div>

            <div class="detail-group">
                <label for="description">Product Description: </label>
                <span class="product-detail">{{ product.description }}</span>
            </div>

            <div class="detail-group">
                <label for="category">Category: </label>
                <span class="product-detail">
                    {{
                        categories.find((cat) => cat.id === product.category_id)
                            ?.name || "N/A"
                    }}
                </span>
            </div>

            <div class="detail-group">
                <label for="discount">Discount: </label>
                <span class="product-detail">{{ product.discount }}</span>
            </div>

            <div class="detail-group">
                <label for="condition">Product Condition: </label>
                <span class="product-detail">
                    {{
                        product_conditions.find(
                            (cond) => cond.id === product.product_condition_id
                        )?.product_condition || "N/A"
                    }}
                </span>
            </div>

            <div class="detail-group">
                <label for="status">Status: </label>
                <span class="product-detail">{{ product.status }}</span>
            </div>
            </div>
            <div class="detail-group">
                <label for="image">Product Image: </label>
                <div v-if="product.image" class="image-preview">
                    <img :src="product.image" alt="Product Image" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useToastr } from "../toastr";
import axios from "axios";

const toastr = useToastr();

export default {
    name: "UpdateProduct",
    data() {
        return {
            product: {
                name: "",
                description: "",
                price: "",
                category_id: null,
                image: null,
                discount: "",
                product_condition_id: null,
                status: "",
            },
            categories: [],
            product_conditions: [],
        };
    },
    methods: {
        // Fetch the product data based on the ID
        getProduct() {
            const id = this.$route.params.id;
            axios
                .get(
                    `${window.location.protocol}//${window.location.host}/api/products/product/${id}`
                )
                .then((response) => {
                    console.log("Product dat:", response);
                    const productData = response.data.product;
                    this.product.name = productData.name;
                    this.product.description = productData.description;
                    this.product.price =
                        (productData.price * 100) /
                        (100 - productData.discount);
                    this.product.image = productData.image;
                    this.product.status = productData.status;
                    this.product.discount = productData.discount;
                    this.product.product_condition_id =
                        productData.product_condition_id;
                    this.product.category_id = response.data.categories[0].id; // If the category_id exists
                    console.log("Product data:", this.product);
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        getCategories() {
            axios
                .get(
                    `${window.location.protocol}//${window.location.host}/api/categories`
                )
                .then((response) => {
                    this.categories = response.data.categories;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        getCondition() {
            axios
                .get(
                    `${window.location.protocol}//${window.location.host}/api/product-conditions`
                )
                .then((response) => {
                    this.product_conditions = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        onFileSelected(event) {
            this.product.image = event.target.files[0];
        },
    },
    created() {
        this.getProduct();
        this.getCategories();
        this.getCondition();
    },
};
</script>

<style scoped>
.product-detail {
    margin-left: 5px;
    color: rgb(91, 79, 79); /* Set text color to pink */
    font-weight: bold;
    
}

.detail-group {
    margin-bottom: 15px;
}

.image-preview img {
    max-width: 100%;
    height: auto;
}
</style>
