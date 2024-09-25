<!-- <template>
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
                            <button class="update delete" @click="goToUpdateProduct(product.id)">Update</button> <button class="delete update" @click="deleteProduct(product.id)">Delete</button> 
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

let table = new DataTable('#myTable');
</script>

<style>
</style> -->
<!-- <template>
  <div class="products-list">
      <h2 class="component-title">{{ title }}</h2>
      <div class="products-list-container">
          <div class="products-list-content">
              <table id=" " class="display">
                  <thead>
                      <tr>
                          <th>Image</th>
                          <th>Name</th>
                          <th>Description</th>
                          <th>Original Price</th>
                          <th>Current Price</th>
                          <th>Discount</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr v-for="product in products" :key="product.id">
                          <td><img :src="product.image" alt="Product Name" width="50"></td>
                          <td>{{ product.name }}</td>
                          <td>{{ product.description }}</td>
                          <td>$ {{ (product.price / (1 - product.discount / 100)).toFixed(2) }}</td>
                          <td>$ {{ product.price }}</td>
                          <td>{{ product.discount }} % off</td>
                          <td>
                              <button class="update delete" @click="goToUpdateProduct(product.id)">Update</button>
                              <button class="delete update" @click="deleteProduct(product.id)">Delete</button>
                          </td>
                      </tr>
                  </tbody>
              </table>
          </div>
          <div class="sidebar">
              <form>
                  <div>
                      <label for="category" class="select-label">Filter by category : </label>
                      <select id="category" v-model="selectedCategory" @change="applyAllChanges()" class="select-filter">
                          <option value="">All</option>
                          <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                      </select>
                  </div>
                  <div>
                      <label for="order" class="select-label">Sort by price : </label>
                      <select id="order" v-model="selectedOrder" @change="applyAllChanges()" class="select-filter">
                          <option value="">Order</option>
                          <option value="ASC">ASC</option>
                          <option value="DESC">DESC</option>
                      </select>
                  </div>
                  <div>
                      <label for="condition" class="select-label">Filter by condition: </label>
                      <select id="conditions" v-model="selectedCondition" @change="applyAllChanges()" class="select-filter">
                          <option value="">All</option>
                          <option v-for="condition in conditions" :key="condition.id" :value="condition.id">{{ condition.product_condition }}</option>
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
let table = new DataTable('#myTable');
import axios from 'axios';

export default {
name: 'ProductsList',

data() {
  return {
    title: 'Products List',
    products: [],
    categories: [],
    conditions: [],
    selectedCategory: '',
    selectedOrder: '',
    selectedCondition: '',
    currentPage: 1,
    totalPages: 1,
    perPage: 3
  };
},

methods: {
  changePage(page) {
    if (page < 1 || page > this.totalPages) return;
    this.currentPage = page;
    this.applyAllChanges(); // Fetch products for the new page
  },

  getProducts(params = {}) {
    params.page = this.currentPage;
    params.limit = this.perPage;
    let queryParams = new URLSearchParams(params).toString();

    axios.get(`${window.location.protocol}//${window.location.host}/api/products?${queryParams}`)
      .then(response => {
        this.products = response.data.products.data;
        this.totalPages = response.data.products.last_page;
        this.$nextTick(() => {
          // Initialize DataTables after DOM update
          $('#productsTable').DataTable();
        });
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
        this.conditions = response.data;
      })
      .catch(error => {
        console.log(error);
      });
  },

  sortByOrder() {
    let params = {};
    if (this.selectedOrder) {
      params.order = this.selectedOrder;
    }
    this.getProducts(params);
  },

  filterByCategory() {
    let params = {};
    if (this.selectedCategory) {
      params.category_id = this.selectedCategory;
    }
    this.getProducts(params);
  },

  filterByCondition() {
    let params = {};
    if (this.selectedCondition) {
      params.condition_id = this.selectedCondition;
    }
    this.getProducts(params);
  },

  applyAllChanges() {
    let params = {};

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

    this.getProducts(params);
  },

  goToUpdateProduct(id) {
    this.$router.push({ path: `/update-product/${id}` });
  },

  deleteProduct(id) {
    axios.delete(`${window.location.protocol}//${window.location.host}/api/products/${id}`)
      .then(response => {
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
/* DataTables CSS - Include in the <head> section */


/* Add custom styles for DataTables if needed */


</style>
 -->
<template>
    <div class="products-list">
        <h2 class="component-title">{{ title }}</h2>
        <div class="products-list-container">
            <div class="products-list-content">
                <div
                    v-for="product in products"
                    :key="product.id"
                    style="width: 100%"
                >
                    <div class="product">
                        <div class="product__image">
                            <img :src="product.image" alt="Product Image" />
                        </div>
                        <div class="product__info">
                            <div class="product_data">
                                <h4>{{ product.name }}</h4>
                                <p>{{ product.description }}</p>
                                <div style="display: flex">
                                    <span
                                        class="original-price"
                                        style="margin-right: 10px"
                                    >
                                        $
                                        {{
                                            (
                                                product.price /
                                                (1 - product.discount / 100)
                                            ).toFixed(2)
                                        }}
                                    </span>
                                    <span>$ {{ product.price }}</span>
                                </div>

                                <span>{{ product.discount }} %off</span>
                            </div>
                            <div class="btngroup">
                                <button
                                    class="update delete"
                                    @click="goToUpdateProduct(product.id)"
                                >
                                    Update
                                </button>
                                <button
                                    class="delete update"
                                    @click="deleteProduct(product.id)"
                                >
                                    Delete
                                </button>
                            </div>
                            <!-- Delete button -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidebar">
                <form>
                    <div>
                        <label for="category" class="select-label"
                            >Filter by category :
                        </label>
                        <select
                            id="category"
                            v-model="selectedCategory"
                            @change="applyAllChanges()"
                            class="select-filter"
                        >
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
                        <label for="order" class="select-label"
                            >Sort by price :
                        </label>
                        <select
                            id="order"
                            v-model="selectedOrder"
                            @change="applyAllChanges"
                            class="select-filter"
                        >
                            <option value="">Order</option>
                            <option value="ASC">ASC</option>
                            <option value="DESC">DESC</option>
                        </select>
                    </div>

                    <div>
                        <label for="condition" class="select-label"
                            >Filter by condition:
                        </label>
                        <select
                            id="conditions"
                            v-model="selectedCondition"
                            @change="applyAllChanges"
                            class="select-filter"
                        >
                            <option value="">All</option>
                            <option
                                v-for="condition in conditions"
                                :key="condition.id"
                                :value="condition.id"
                            >
                                {{ condition.product_condition }}
                            </option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="pagination">
            <button
                @click="changePage(currentPage - 1)"
                :disabled="currentPage === 1"
            >
                Previous
            </button>
            <span>Page {{ currentPage }} of {{ totalPages }}</span>
            <button
                @click="changePage(currentPage + 1)"
                :disabled="currentPage === totalPages"
            >
                Next
            </button>
        </div>
    </div>
</template>

<script>
import axios from "axios";
export default {
    name: "ProductsList",

    data() {
        return {
            title: "Products List",
            products: [],
            categories: [],
            conditions: [],
            originalProducts: [],
            selectedCategory: "",
            selectedOrder: "",
            selectedCondition: "", // Add this property
            currentPage: 1,
            totalPages: 1, // Add this for pagination
            perPage: 10,
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
            // params.page = this.currentPage;
            // params.limit = this.perPage;

            let queryParams = new URLSearchParams(params).toString();

            axios
                .get(
                    `${window.location.protocol}//${window.location.host}/api/products?${queryParams}`
                )
                .then((response) => {
                    this.products = response.data.products.data;
                    this.totalPages = response.data.products.last_page; // Get total pages from the response
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        getCategories: function () {
            axios
                .get(
                    `${window.location.protocol}//${window.location.host}/api/categories`
                )
                .then((response) => {
                    this.categories = response.data.categories;
                    console.log("iii", this.categories);
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        getCondition: function () {
            axios
                .get(
                    `${window.location.protocol}//${window.location.host}/api/product-conditions`
                )
                .then((response) => {
                    this.conditions = response.data; // Ensure this.data is the array you expect
                    console.log("iiiw", this.conditions);
                })
                .catch((error) => {
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

        // sortByOrder: function () {
        //     let params = {};
        //     if (this.selectedOrder) {
        //         params.order = this.selectedOrder; // Ensure this matches the API filter parameter
        //     }
        //     console.log("parames99", params);

        //     this.getProducts(params);
        // },

        // filterByCategory(e) {
        //     console.log("aa", e.target.value, e.target.id);
        //     let params = {};
        //     if (this.selectedCategory) {
        //         params.category_id = this.selectedCategory; // Ensure this matches the API filter parameter
        //     }
        //     console.log("parames", params);

        //     this.getProducts(params); // Fetch filtered products from server
        // },

        // filterByCondition() {
        //     let params = {};
        //     if (this.selectedCondition) {
        //         params.condition_id = this.selectedCondition; // Ensure this matches the API filter parameter
        //     }
        //     console.log("paramesv", params);

        //     this.getProducts(params); // Fetch filtered products from server
        // },

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
            this.$router.push({ path: `/update-product/${id}` }); // Navigate to UpdateProduct component
        },

        deleteProduct(id) {
            axios
                .delete(
                    `${window.location.protocol}//${window.location.host}/api/products/${id}`
                )
                .then((response) => {
                    // Remove the deleted product from the local list
                    this.products = this.products.filter(
                        (product) => product.id !== id
                    );
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },

    created() {
        this.getProducts();
        this.getCategories();
        this.getCondition();
    },
};
</script>

<style>
.products-list {
    max-width: 1300px;
    margin: 0 auto;
    padding: 20px;
    border-radius: 12px;
}

.component-title {
    font-size: 24px;
    margin-bottom: 20px;
    color: #c2185b; /* Darker pink for the title */
}

/* Products list container */
.products-list-container {
    display: flex;
    gap: 20px;
}

/* Products list content */
.products-list-content {
    flex: 3;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

/* Individual product styling */
.product {
    display: flex;
    align-items: center;
    background: #ffffff; /* White background for products */
    border-radius: 12px;
    padding: 15px;
    flex: 1 1 calc(33.333% - 20px); /* Responsive product cards */
    transition: transform 0.3s;
}

.product:hover {
    transform: scale(1.02); /* Slight zoom effect on hover */
}

.product__image img {
    width: 200px;
    border-radius: 8px;
    margin-right: 20px;
}

/* Product info */
.product__info {
    margin-top: 10px;
}

.product_data h4 {
    color: #c2185b; /* Dark pink for product name */
}

.original-price {
    text-decoration: line-through;
    color: #999; /* Grey for original price */
}

/* Button group styling */
.btngroup {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.update {
    background: #f06292; /* Bright pink for update button */
    color: #fff;
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
}

.update:hover {
    background: #e91e63; /* Darker pink on hover */
}

.delete {
    background: #ff4081; /* Slightly different pink for delete button */
    color: #fff;
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
}

.delete:hover {
    background: #f50057; /* Darker pink on hover */
}

/* Sidebar styling */
.sidebar {
    flex: 1;
    background: #f8f8f8; /* Light grey for sidebar */
    padding: 20px;
    border-radius: 12px;
    box-shadow: 4px 4px 8px #bcbcbc, -4px -4px 8px #ffffff; /* Neumorphism shadow */
}

/* Select and label styling */
.select-label {
    display: block;
    margin-bottom: 5px;
    color: #c2185b; /* Dark pink for labels */
}

.select-filter {
    width: 100%;
    padding: 10px;
    border: 1px solid #ffb6c1;
    border-radius: 8px;
    transition: border-color 0.3s;
}

.select-filter:focus {
    border-color: #c2185b; /* Focused state with darker pink */
    outline: none;
}

/* Pagination styling */
.pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 20px;
}

.pagination button {
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    background: #f06292; /* Bright pink for pagination buttons */
    color: #fff;
    cursor: pointer;
    transition: background 0.3s;
}

.pagination button:hover {
    background: #e91e63; /* Darker pink on hover */
}
</style>
