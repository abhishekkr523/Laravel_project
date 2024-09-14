<template>
    <div class="products-list">
        <h2 class="component-title">{{ title }}</h2>
        <div class="products-list-container">
            <div class="products-list-content">
                <div v-for="product in products" :key="product.id">
                    <div class="product">
                        <div class="product__image">
                            <img :src="product.image" alt="Product Name">
                        </div>
                        <div class="product__info">
                            <h4>{{ product.name }}</h4>
                            <p>{{ product.description }}</p>
                            <span class="original-price">
                                    $ {{ (product.price / (1 - product.discount / 100)).toFixed(2) }}
                                </span>
                            <span>$ {{ product.price }}</span><br>
                            
                            <span>{{ product.discount }} %off</span>
                            <button class="update delete" @click="goToUpdateProduct(product.id)">Update</button> <button class="delete update" @click="deleteProduct(product.id)">Delete</button> <!-- Delete button -->
                        </div>
                    </div>
                 </div>
            </div>
            <div class="sidebar">
                <form>
                    <div>
                        <label for="category" class="select-label">Filter by category : </label>
                        <select
                            id="category"
                            v-model="selectedCategory"
                            @change="applyAllChanges()"
                            class="select-filter">
                                <option value="">All</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                        </select>
                     </div>
                     <div>
                        <label for="order" class="select-label">Sort by price : </label>
                        <select
                            id="order"
                            v-model="selectedOrder"
                            @change="applyAllChanges"
                            class="select-filter"
                        >
                            <option value="" >Order </option>
                            <option value="ASC">ASC</option>
                            <option value="DESC">DESC</option>
                        </select>
                     </div>
    
                     <div>
                      <label for="condition" class="select-label">Filter by condition: </label>
                      <select id="conditions" v-model="selectedCondition" @change="applyAllChanges" class="select-filter">
                       <option value="">All</option>
                       <option
                         v-for="condition in conditions"
                         :key="condition.id"
                         :value="condition.id">
                         {{ condition.product_condition }}
                       </option>
                     </select>


                    </div>

                </form>
            </div>
        </div>
        <div class="pagination">
  <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
  <span>Page {{ currentPage }} of {{ totalPages }}</span>
  <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages">Next</button>
</div>

    </div>
</template>

<script>

   export default {
  name: 'ProductsList',

  data() {
    return {
      title: 'Products List',
      products: [],
      categories: [],
      conditions: [], 
      originalProducts: [], 
      selectedCategory: '',
      selectedOrder: '',
      selectedCondition: '',  // Add this property
      currentPage: 1,
      totalPages: 1, // Add this for pagination
      perPage: 3
    };
  },

  methods: {
changePage(page) {
    if (page < 1 || page > this.totalPages) return;
    this.currentPage = page;
    this.applyAllChanges(); // Fetch products for the new page
  },

    // getProducts: function (params = {}) {
    //       console.log("params1",params)
    //       let queryParams = new URLSearchParams(params).toString();
    //       console.log("parames2",queryParams)
    //       axios.get(`${window.location.protocol}//${window.location.host}/api/products?${queryParams}`)
    //         .then(response => {
    //           console.log("jpp",response);
    //           this.products = response.data.products.data;
    //         })
    //         .catch(error => {
    //           console.log(error);
    //         });
    // },

    getProducts(params = {}) {
  // Add pagination parameters
  params.page = this.currentPage;
  params.limit = this.perPage;

  let queryParams = new URLSearchParams(params).toString();

  axios.get(`${window.location.protocol}//${window.location.host}/api/products?${queryParams}`)
    .then(response => {
      this.products = response.data.products.data;
      this.totalPages = response.data.products.last_page; // Get total pages from the response
    })
    .catch(error => {
      console.log(error);
    });
},



    getCategories: function () {
      axios.get(`${window.location.protocol}//${window.location.host}/api/categories`)
        .then(response => {
          this.categories = response.data.categories;
          console.log("iii", this.categories);
        })
        .catch(error => {
          console.log(error);
        });
    },

   getCondition: function () {
    axios.get(`${window.location.protocol}//${window.location.host}/api/product-conditions`)
    .then(response => {
      this.conditions = response.data;  // Ensure this.data is the array you expect
      console.log("iiiw", this.conditions);
    })
    .catch(error => {
      console.log(error);
    });
   },

    // filterByCategory: function () {
    //   axios.get(`${window.location.protocol}//${window.location.host}/api/products/${this.selectedCategory}`)
    //     .then(response => {
    //       this.products = response.data.products;
    //       console.log("filterByCategory",this.products)
    //     })
    //     .catch(error => {
    //       console.log(error);
    //     });
    // },

    // sortByOrder: function () {
    //   if (this.selectedOrder === "ASC") {
    //     this.products = this.products.sort((a, b) => a.price - b.price);
    //   }
    //   if (this.selectedOrder === "DESC") {
    //     this.products = this.products.sort((a, b) => b.price - a.price);
    //   }
    // },

    sortByOrder: function () {
      let params = {};
      if (this.selectedOrder) {
      params.order = this.selectedOrder; // Ensure this matches the API filter parameter
      }
      console.log("parames99",params)
      
      this.getProducts(params);
    },

    filterByCategory(e) {
        console.log("aa", e.target.value,e.target.id)
      let params = {};
      if (this.selectedCategory) {
      params.category_id = this.selectedCategory; // Ensure this matches the API filter parameter
      }
      console.log("parames",params)
      
      this.getProducts(params);  // Fetch filtered products from server
    },

    filterByCondition() {
      let params = {};
      if (this.selectedCondition) {
      params.condition_id = this.selectedCondition; // Ensure this matches the API filter parameter
      }
      console.log("paramesv",params)
      
      this.getProducts(params);  // Fetch filtered products from server
    },

    applyAllChanges() {
       let params = {};

       // Only add params if they have values
       if (this.selectedCondition) {
       params.condition_id = this.selectedCondition;
       }

       if (this.selectedCategory) {
         params.category_id = this.selectedCategory;
       }

       if (this.selectedOrder) {
         params.order = this.selectedOrder;
       }

 params.page = this.currentPage;
  params.limit = this.perPage;
  
       console.log("All Params:", params);
       this.getProducts(params);
    },

    goToUpdateProduct(id) {
      this.$router.push({ path: `/update-product/${id}` });  // Navigate to UpdateProduct component
    },

    deleteProduct(id) {
      axios.delete(`${window.location.protocol}//${window.location.host}/api/products/${id}`)
        .then(response => {
          // Remove the deleted product from the local list
          this.products = this.products.filter(product => product.id !== id);
        })
        .catch(error => {
          console.log(error);
        });
    }
  },

  created() {
    this.getProducts();
    this.getCategories();
    this.getCondition();
  }
};


</script>

<style>

</style>
