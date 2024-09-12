<template>
    <div class="add-product">
        <h2 class="component-title">{{ title }}</h2>
        <form @submit.prevent="addProduct">
            <div class="form-first-group">
                <div>
                    <div class="first-input">
                        <label for="name">Product Name</label>
                        <input type="text" v-model="product.name" id="name">
                     </div>
                     <div>
                         <label for="price">Product Price</label>
                         <input type="number" v-model="product.price" id="price">
                     </div>
                </div>
                <div>
                     <label for="description">Product Description</label>
                     <textarea v-model="product.description" id="description"></textarea>
                </div>
            </div>

            <!-- New Section for Showing Discounted Price -->
            <div class="form-second-group">
                <div>
                    <label for="discount">Discount (%)</label>
                    <input type="number" v-model="product.discount" id="discount">
                </div>
                <div>
                    <label for="discountedPrice">Price After Discount</label>
                    <input type="text" :value="discountedPrice" id="discountedPrice" readonly>
                </div>
            </div>

            <div class="form-second-group">
                <div>
                    <label for="image">Product Image</label>
                    <input type="file" id="image" accept="image/*" v-on:change="onFileSelected">
                </div>
                <div>
                    <label for="category">Product Category</label>
                    <select v-model="product.category_id" id="category">
                        <option value="">Select Category</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="form-second-group">
                <div>
                    <label for="condition">Product Condition</label>
                    <select v-model="product.product_condition_id" id="condition">
                        <option value="">Select Condition</option>
                        <option v-for="condition in product_conditions" :key="condition.id" :value="condition.id">{{ condition.product_condition }}</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="submit--btn">Add Product</button>
        </form>
    </div>
</template>

<script>
    import { useToastr } from "../toastr";
    const toastr = useToastr();

    export default {
        name: 'AddProduct',
        data() {
            return {
                title: 'Add Product',
                categories: [],
                product_conditions: [],
                product: {
                    name: '',
                    description: '',
                    price: '',
                    category_id: null,
                    image: null,
                    discount: 0,
                    product_condition_id: null
                }
            }
        },

        methods: {
            getCategories() {
                axios.get(`${window.location.protocol}//${window.location.host}/api/categories`)
                    .then(response => {
                        this.categories = response.data.categories;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },

            getCondition() {
                axios.get(`${window.location.protocol}//${window.location.host}/api/product-conditions`)
                    .then(response => {
                        this.product_conditions = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },

            onFileSelected(event) {
                this.product.image = event.target.files[0];
            },

            addProduct() {
                const formData = new FormData();
                formData.append('name', this.product.name);
                formData.append('description', this.product.description);
                formData.append('price', this.product.price);
                formData.append('category_id', this.product.category_id);
                formData.append('discount', this.product.discount || 0); // Default discount to 0
                formData.append('product_condition_id', this.product.product_condition_id);
                if (this.product.image) {
                    formData.append('image', this.product.image);
                }

                axios.post(`${window.location.protocol}//${window.location.host}/api/products`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                    .then(response => {
                        toastr.success("Product created successfully!");
                        this.$router.push({ path: '/' });
                    })
                    .catch(error => {
                        const errors = JSON.parse(error.request.response).errors;
                        for (const property in errors) {
                            toastr.warning(`${errors[property]}`);
                        }
                    });
            }
        },

        computed: {
            // Computed property to calculate the discounted price
            discountedPrice() {
                const discountAmount = (this.product.price * this.product.discount) / 100;
                const finalPrice = this.product.price - discountAmount;
                return finalPrice.toFixed(2); // Keep two decimal places for precision
            }
        },

        created() {
            this.getCategories();
            this.getCondition();
        }
    }
</script>

<style>
/* Add your styles here */
</style>
