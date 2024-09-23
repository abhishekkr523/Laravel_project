<template>
    <div class="update-product">
        <h2 class="component-title">Update Product</h2>
        <form @submit.prevent="updateProduct" class="update-form">
            <div class="form-first-group">
                <div class="input">
                    <label for="name">Product Name</label>
                    <input
                        type="text"
                        v-model="product.name"
                        id="name"
                        class="form-input"
                    />
                </div>

                <div class="input">
                    <label for="price">Product Price</label>
                    <input
                        type="number"
                        v-model="product.price"
                        id="price"
                        class="form-input"
                    />
                </div>
            </div>
            <div class="form-second-group">
                <div class="input-group section">
                    <label for="description">Product Description</label>
                    <textarea
                        v-model="product.description"
                        id="description"
                        class="form-textarea"
                    ></textarea>
                </div>
                
                <div class="input-group">
                    <label for="category">Category</label>
                    <select
                        v-model="product.category_id"
                        id="category"
                        class="form-select"
                    >
                        <option value="">Select Category</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-second-group">
                <div class="input-group">
                    <label for="discount">Discount</label>
                    <input
                        type="number"
                        v-model="product.discount"
                        id="discount"
                        class="form-input"
                    />
                </div>
                <div class="input-group">
                    <label for="condition">Product Condition</label>
                    <select
                        v-model="product.product_condition_id"
                        id="condition"
                        class="form-select"
                    >
                        <option value="">Select Condition</option>
                        <option
                            v-for="condition in product_conditions"
                            :key="condition.id"
                            :value="condition.id"
                        >
                            {{ condition.product_condition }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <label for="image" style=" display:flex; align-items:start;">Product Image</label>
                <input
                    type="file"
                    id="image"
                    accept="image/*"
                    v-on:change="onFileSelected"
                    class="form-input"
                />
                
            </div>
            <div class="display-image" v-if="product.image" style="margin-left:115px;">
                <img
                    :src="product.image"
                    alt="Product Image"
                    class="image-preview"
                />
            </div>
            <button type="submit" class="submit--btn">Update Product</button>
        </form>
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
                    this.product.price = productData.price;
                    this.product.image = productData.image;
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
        // updateProduct() {
        //     console.log("ooo")
        //     const formData = new FormData();
        //     formData.append('name', this.product.name);
        //     formData.append('description', this.product.description);
        //     formData.append('price', this.product.price);
        //     formData.append('category_id', this.product.category_id);
        //     formData.append('discount', this.product.discount);
        //     formData.append('product_condition_id', this.product.product_condition_id);
        //     if (this.product.image) {
        //         formData.append('image', this.product.image);
        //     }

        //     const id = this.$route.params.id;
        //     console.log('jjj',formData)
        //     // Use the resource-based route for update (PUT request)
        //     axios.put(`${window.location.protocol}//${window.location.host}/api/products/${id}`, formData, {
        //      headers: {
        //            'Content-Type': 'multipart/form-data'
        //         }
        //     })
        //     .then(response => {
        //         console.log('jj',response)
        //         toastr.success("Product updated successfully!");
        //         this.$router.push({ path: '/' });
        //     })
        //     .catch(error => {
        //         const errors = JSON.parse(error.request.response).errors;
        //         for (const property in errors) {
        //          toastr.warning(`${errors[property]}`);
        //      }
        //     });
        // }

        updateProduct() {
            const formData = new FormData();
            formData.append("name", this.product.name);
            formData.append("description", this.product.description);
            formData.append("price", this.product.price);
            formData.append("category_id", this.product.category_id);
            formData.append("discount", this.product.discount);
            formData.append(
                "product_condition_id",
                this.product.product_condition_id
            );
            formData.append("_method", "PUT");
            if (this.product.image) {
                formData.append("image", this.product.image);
            }

            const id = this.$route.params.id;

            console.log("FormData contents:");
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            axios
                .post(
                    `${window.location.protocol}//${window.location.host}/api/products/${id}`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                )

                .then((response) => {
                    console.log("Responsee:", response);
                    toastr.success("Product updated successfully!");
                    this.$router.push({ path: "/" });
                })
                .catch((error) => {
                    const errors = JSON.parse(error.request.response).errors;
                    for (const property in errors) {
                        toastr.warning(`${errors[property]}`);
                    }
                });
        },
    },
    created() {
        this.getProduct();
        this.getCategories();
        this.getCondition();
    },
};
</script>

<style>
.update-product {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 8px 8px 16px #fbeaed, -8px -8px 16px #ffffff; /* Neumorphism shadow */
}

.component-title {
    font-size: 24px;
    margin-bottom: 20px;
    color: #c2185b; /* Darker pink for the title */
}

/* Form styling */
.update-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Input styling */
.input {
    display: flex;
    flex-direction: column;
}

.form-input,
.form-textarea,
.form-select {
    padding: 10px;
    border: 1px solid #ffb6c1;
    border-radius: 8px;
    background: #ffffff; /* White background for inputs */
    transition: border-color 0.3s;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    border-color: #c2185b; /* Focused state with darker pink */
    outline: none;
}

/* Textarea styling */
.form-textarea {
    height: 100px;
}

/* Button styling */
.submit--btn {
    padding: 10px;
    border: none;
    border-radius: 8px;
    background: #f06292; /* Bright pink for button */
    color: #fff;
    cursor: pointer;
    transition: background 0.3s;
}

.submit--btn:hover {
    background: #e91e63; /* Darker pink on hover */
}

/* Image preview styling */
.display-image {
    display: flex;
    justify-content: center;
    margin-top: 10px;
}

.image-preview {
    max-width: 100%;
    border-radius: 8px;
    box-shadow: 2px 2px 8px #ffb6c1; /* Light pink shadow */
}

/* Label styling */
label {
    color: #c2185b; /* Dark pink for labels */
    margin-bottom: 5px;
}
</style>