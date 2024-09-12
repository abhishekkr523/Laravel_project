<template>
    <div class="update-product">
        <h2 class="component-title">Update Product</h2>
        <form @submit.prevent="updateProduct">
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
            <div class="form-second-group">
                <div>
                    <label for="image">Product Image</label>
                    <div class="display-image" v-if="product.image">
                       <img :src="product.image" alt="Product Image" class="image-preview">
                     </div>
                    <input type="file" id="image" accept="image/*" v-on:change="onFileSelected">
                </div>
                <div>
                   <select v-model="product.category_id" id="category">
                        <option value="">Select Category</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                   </select>

                </div>
            </div>
            <div class="form-second-group">
                <div>
                         <label for="discount">Discount</label>
                         <input type="number" v-model="product.discount" id="discount">
                </div>
                <div>
                    <label for="condition">Product Condition</label>
                    <select v-model="product.product_condition_id" id="condition">
                        <option value="">Select Condition</option>
                        <option v-for="condition in product_conditions" :key="condition.id" :value="condition.id">{{ condition.product_condition }}</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="submit--btn" >Update Product</button>
        </form>
    
    </div>
</template>

<script>
    import { useToastr } from "../toastr";

    const toastr = useToastr();

    export default {
        name: 'UpdateProduct',
        data() {
            return {
                product: {
                    name: '',
                    description: '',
                    price: '',
                    category_id: null,
                    image: null,
                    discount: '',
                    product_condition_id: null,
                },
                categories: [],
                product_conditions: []
            };
        },
        methods: {
            // Fetch the product data based on the ID
         getProduct() {
          const id = this.$route.params.id;
          axios.get(`${window.location.protocol}//${window.location.host}/api/products/product/${id}`)
          .then(response => {
          console.log("Product dat:", response);
          const productData = response.data.product;
          this.product.name = productData.name;
          this.product.description = productData.description;
          this.product.price = productData.price;
          this.product.image = productData.image;
          this.product.discount = productData.discount;
          this.product.product_condition_id = productData.product_condition_id;
          this.product.category_id = response.data.categories[0].id;  // If the category_id exists
          console.log("Product data:", this.product);
          })
           .catch(error => {
           console.log(error);
          });
           },



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
    formData.append('name', this.product.name);
    formData.append('description', this.product.description);
    formData.append('price', this.product.price);
    formData.append('category_id', this.product.category_id);
    formData.append('discount', this.product.discount);
    formData.append('product_condition_id', this.product.product_condition_id);
    formData.append('_method', 'PUT');
    if (this.product.image) {
        formData.append('image', this.product.image);
    }

    const id = this.$route.params.id;
    
   console.log('FormData contents:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    axios.post(`${window.location.protocol}//${window.location.host}/api/products/${id}`, formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    
    .then(response => {
        console.log('Responsee:', response);
        toastr.success("Product updated successfully!");
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
        created() {
            this.getProduct();
            this.getCategories();
            this.getCondition();
        }
    }
</script>

<style>
/* Add your styles here */
</style>
