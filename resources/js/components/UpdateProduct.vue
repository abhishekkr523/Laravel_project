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

<style scoped>
.update-product {
    background-color: #e0e0e0;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 8px 8px 16px rgba(0, 0, 0, 0.2),
        -8px -8px 16px rgba(255, 255, 255, 0.5);
}

.component-title {
    font-size: 24px;
    margin-bottom: 20px;
    text-align: center;
}

.update-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-first-group,
.form-second-group {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    height: 100px;
}

.input-group {
    flex: 1;
    height: 42px;
}

.input,
.section {
    display: flex;
    flex-direction: row;
    align-items: center;
    width: 50%;
}


.input label {
    margin-bottom: 5px;
    font-weight: bold;
}

.form-input,
.form-select,
.form-textarea {
    height: 42px;
    background: #e0e0e0;
    border: none;
    border-radius: 10px;
    padding: 10px;
    font-size: 16px;
    box-shadow: inset 6px 6px 12px rgba(0, 0, 0, 0.2),
        inset -6px -6px 12px rgba(255, 255, 255, 0.5);
    transition: all 0.3s ease;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    box-shadow: inset 3px 3px 6px rgba(0, 0, 0, 0.3),
        inset -3px -3px 6px rgba(255, 255, 255, 0.7);
    outline: none;
}

.form-textarea {
    resize: none;
    height: 100px;
}

.display-image {
    width: 150px;
    margin: 10px 0;
    text-align: center;
}

.image-preview {
    max-width: 100%;
    border-radius: 10px;
    box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2),
        -4px -4px 8px rgba(255, 255, 255, 0.5);
}

.submit--btn {
    background-color: #f0a;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.3),
        -2px -2px 6px rgba(255, 255, 255, 0.5);
    transition: all 0.3s ease;
}

.submit--btn:hover {
    background-color: rgb(225, 14, 155);
}
label{
    display: flex;
    align-items: center;
    font-weight: bold;
    margin-right: 5px;
}
</style>
