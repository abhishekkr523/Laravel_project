<template>
    <div>
        <div class="filter_data">
            <div class="filter-container">
                <div class="filter-item">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search Products"
                        class="custom-search"
                        @input="performCustomSearch"
                    />
                    <i class="fa fa-search search-icon"></i>
                </div>

                <div class="filter-item">
                    <select
                        id="pageLimit"
                        v-model="pageLimit"
                        class="page-limit-select"
                        @click="getLimit"
                    >
                    <option value="1">1</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <i class="fa fa-list-ul page-limit-icon"></i>
                </div>

                <div class="filter-item">
                    <button @click="toggleFilterForm" class="filter-btn">
                        <i class="fa fa-filter filter-icon"></i> Filter
                    </button>
                </div>
                <div class="up">
                    <form @submit.prevent="uploadFile">
                        <input type="file" ref="fileInput" name="file" />
                        <button type="submit" class="export-button">Import Products</button>
                    </form>
                    <div v-if="message">{{ message }}</div>
                </div>
                <span>
                    <button @click="exportData" class="export-button">
                        Export Data
                    </button>
                </span>
            </div>

            <!-- Filter Form (conditionally shown) -->
            <form v-if="showFilterForm" class="filter-form">
                <div class="form-group">
                    <label for="category" class="select-label">
                        <i class="fa fa-tags category-icon"></i> Filter by
                        category:
                    </label>
                    <select
                        id="category"
                        v-model="selectedCategory"
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

                <div class="form-group">
                    <label for="order" class="select-label">
                        <i class="fa fa-sort-amount-up order-icon"></i> Sort by
                        price:
                    </label>
                    <select
                        id="order"
                        v-model="selectedOrder"
                        class="select-filter"
                    >
                        <option value="">Order</option>
                        <option value="ASC">ASC</option>
                        <option value="DESC">DESC</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="condition" class="select-label">
                        <i class="fa fa-info-circle condition-icon"></i> Filter
                        by condition:
                    </label>
                    <select
                        id="conditions"
                        v-model="selectedCondition"
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

                <!-- Apply and Reset buttons -->
                <div
                    class="form-group"
                    style="align-content: end; margin: 0px 0px 15px 4px"
                >
                    <button
                        @click.prevent="applyAllChanges"
                        class="btn btn-primary m-1"
                    >
                        Apply
                    </button>
                    <button
                        type="reset"
                        @click.prevent="resetFilters"
                        class="btn btn-secondary"
                    >
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <table id="example" class="display" style="width: 100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Discount</th>
                    <th>Condition</th>
                    <th>Categories</th>
                </tr>
            </thead>
            <!-- <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Discount</th>
                    <th>Condition</th>
                    <th>Categories</th>
                </tr>
            </tfoot> -->
        </table>

        <div>
            <div class="overflow-auto">
                <!-- Pagination Component -->
                <b-pagination
                    v-model="currentPage"
                    :total-rows="rows"
                    :per-page="perPage"
                    aria-controls="example"
                />

                <!-- Current Page Indicator -->
                <p class="mt-3">Current Page: {{ currentPage }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net";
import "datatables.net-buttons";
import "datatables.net-buttons/js/buttons.html5.js";
import "datatables.net-buttons-dt/css/buttons.dataTables.min.css";
import "datatables.net-buttons/js/buttons.print.js";
import "datatables.net-buttons/js/buttons.colVis.js";
import "bootstrap/dist/css/bootstrap.min.css";
import "jszip";
import { ref, onMounted } from "vue";
import { Bootstrap5Pagination } from "laravel-vue-pagination";

export default {
    components: {
        Bootstrap5Pagination,
    },
    data() {
        return {
            table: null,
            showFilterForm: false,
            message: "",
            pageLimit: 10,
            totalPages: 1,
            searchQuery: "",
            products: [],
            categories: [],
            conditions: [],
            originalProducts: [],
            selectedCategory: "",
            selectedOrder: "",
            selectedCondition: "",
            currentPage: 1,
            param: {
                currentPage: 1,
                pageLimit: 1,
            },
            laravelData: {
                current_page: 1,
                data: [],
                last_page: 1,
                total: 0,
            },
        };
    },
    computed: {
        rows() {
            return this.laravelData.total || 0; // Update with total rows after filtering
        },
        perPage() {
            return Number(this.pageLimit);
        },
    },

    // watch: {
    watch: {
        currentPage(newPage) {
            this.getResults(newPage);
        },
    },

    methods: {
        handleClick(pageNumber) {
            // your handling logic here
            console.log("pageNumber", pageNumber);
        },

        toggleFilterForm() {
            this.showFilterForm = !this.showFilterForm; // Toggle form visibility
        },

        resetFilters() {
            this.selectedCategory = "";
            this.selectedOrder = "";
            this.selectedCondition = "";
        },

        exportData() {
            // Call the API route to export products
            window.location.href = `${window.location.origin}/api/export-products`;
        },

        getLimit() {
            axios
                .get(
                    `http://127.0.0.1:8000/api/products?page=${1}&limit=${
                        this.pageLimit
                    }`
                )
                .then((response) => {
                    this.laravelData = response.data.products;
                    this.updateTableData(this.laravelData.data);
                })
                .catch((error) => {
                    console.error("Error fetching data:", error);
                });
        },

        getResults(page = 1) {
            axios
                .get(
                    `http://127.0.0.1:8000/api/products?page=${page}&limit=${this.pageLimit}`
                )
                .then((response) => {
                    this.laravelData = response.data.products;
                    this.updateTableData(this.laravelData.data);
                })
                .catch((error) => {
                    console.error("Error fetching data:", error);
                });
        },
        updateTableData(data) {
            if (this.table) {
                this.table.clear();
                this.table.rows.add(data);
                this.table.draw();
            }
        },

        getCategories() {
            axios
                .get("/api/categories")
                .then((response) => {
                    this.categories = response.data.categories;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        getCondition() {
            axios
                .get("/api/product-conditions")
                .then((response) => {
                    this.conditions = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        applyAllChanges() {
            if (this.table) {
                let params = {
                    page: 1,
                    limit: this.pageLimit,
                };
                if (this.selectedCondition) {
                    params.condition_id = this.selectedCondition;
                }
                if (this.selectedCategory) {
                    params.category_id = this.selectedCategory;
                }
                if (this.selectedOrder) {
                    params.order = this.selectedOrder;
                }

                const queryString = new URLSearchParams(params).toString();
                const apiUrl = `/api/products?${queryString}`;

                // Make the AJAX call for filtered data
                axios
                    .get(apiUrl)
                    .then((response) => {
                        const productsData = response.data.products;

                        // Update the laravelData object
                        this.laravelData = productsData;

                        // Update the table with new filtered data
                        this.updateTableData(productsData.data);

                        // Reset the current page
                        this.currentPage = 1;
                        this.searchQuery = "";
                        this.showFilterForm = false;
                    })
                    .catch((error) => {
                        console.error("Error fetching filtered data:", error);
                    });
            } else {
                console.error("DataTable instance is not initialized.");
            }
        },
        async uploadFile() {
            const fileInput = this.$refs.fileInput;

            if (!fileInput.files.length) {
                this.message = "Please select a file to upload.";
                return;
            }

            const file = fileInput.files[0];
            const formData = new FormData();
            formData.append("file", file);

            try {
                const response = await axios.post(
                    "/api/products/import",
                    formData,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                    }
                );
                this.message = response.data.message;
                fileInput.value = "";
                setTimeout(() => {
                    this.message = "";
                }, 10000);
            } catch (error) {
                console.error("Error while importing the file:", error);
                this.message = "An error occurred while importing the file.";
            }
        },
        loadPageData(page) {
            if (this.table) {
                this.table.ajax
                    .url(
                        `/api/products?page=${page}&search=${this.searchQuery}`
                    )
                    .load();
            }
        },
        performCustomSearch() {
            this.resetFilters();
            if (this.table) {
                this.table.ajax
                    .url(
                        `/api/products?page=1&search=${this.searchQuery}&condition=${this.selectedCategory}`
                    )
                    .load();
            }
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.table = $("#example").DataTable({
                paging: false,
                searching: false,
                ajax: {
                    url: "/api/products",
                    dataSrc: (json) => {
                        this.totalPages = Math.ceil(
                            json.products.total / this.pageLimit
                        );
                        // this.createPaginationUI(this.totalPages);
                        return json.products.data;
                    },
                    data: (d) => {
                        d.limit = this.pageLimit;
                    },
                },
                columns: [
                    { title: "ID", data: "id" },
                    { title: "Name", data: "name" },
                    { title: "Description", data: "description" },
                    { title: "Price", data: "price" },
                    {
                        title: "Image",
                        data: "image",
                        render: (data, type, row) => {
                            return data
                                ? `<img src="${data}" alt="${row.name}" style="width: 100px; height: auto;">`
                                : "No Image";
                        },
                    },
                    { title: "Discount", data: "discount" },
                    {
                        title: "Condition",
                        data: "product_con.product_condition",
                    },
                    {
                        title: "Categories",
                        data: (row) =>
                            row.categories.map((cat) => cat.name).join(", "),
                    },
                ],
                dom: '<"top"lBf>rt<"bottom"ip><"clear">',
                buttons: [
                    {
                        extend: "copyHtml5",
                        text: "Copy",
                    },
                    {
                        extend: "csvHtml5",
                        text: "CSV",
                    },
                    {
                        extend: "excelHtml5",
                        text: "Excel",
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                    },
                    {
                        extend: "print",
                        text: "Print",
                    },
                    {
                        extend: "colvis",
                        text: "Column Visibility",
                    },
                ],
            });

            this.getCategories();
            this.getCondition();
            // this.initPaginationControls();
        });

        this.getResults(this.laravelData.current_page);
        this.getLimit();
    },
};
</script>

<style>
/* General Styling */
body {
    font-family: "Arial", sans-serif;
    background-color: #ffe6f2;
    color: #333;
}

.filter_data {
    background-color: #fff0f5;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 20px;
}

.filter-container {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}

.filter-item {
    display: flex;
    align-items: center;
}

.custom-search {
    padding: 10px;
    border-radius: 20px;
    border: 2px solid #ff99cc;
    outline: none;
    width: 200px;
    transition: all 0.3s ease;
}

.custom-search:focus {
    border-color: #ff66b2;
    box-shadow: 0 0 8px #ff66b2;
}

.search-icon {
    margin-left: -30px;
    color: #ff66b2;
}
.up{
    display: flex;
    align-items: center;
    justify-content: center;  
}
.up form{
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
}
.up form input{
    width: 230px;
}
.page-limit-select {
    padding: 8px;
    border-radius: 20px;
    border: 2px solid #ff99cc;
    background-color: #ffe6f2;
    color: #333;
}

.page-limit-icon {
    margin-left: 10px;
    color: #ff66b2;
}

.filter-btn {
    background-color: #ff66b2;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 20px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.filter-btn:hover {
    background-color: #ff3385;
}

.export-button {
    background-color: #ff66b2;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 20px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.export-button:hover {
    background-color: #ff3385;
}

/* Filter Form */
.filter-form {
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    background: rgb(214, 212, 212);
}

.form-group {
    margin-bottom: 15px;
}

.select-label {
    display: block;
    font-weight: bold;
    color: #ff66b2;
    margin-bottom: 8px;
}

.select-filter {
    width: 100%;
    padding: 8px;
    border-radius: 8px;
    border: 2px solid #ff99cc;
}

.category-icon,
.order-icon,
.condition-icon {
    margin-right: 8px;
    color: #ff66b2;
}

.btn-primary {
    background-color: #ff66b2;
    border-color: #ff66b2;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
}

.btn-primary:hover {
    background-color: #ff3385;
}

.btn-secondary {
    background-color: #fff0f5;
    border-color: #ff99cc;
    color: #ff66b2;
    padding: 8px 16px;
    border-radius: 20px;
}

.btn-secondary:hover {
    background-color: #ff99cc;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
}

th,
td {
    padding: 12px;
    border: 1px solid #ff99cc;
    text-align: left;
}

thead th {
    background-color: #ff66b2;
    color: white;
}

tfoot th {
    background-color: #ffe6f2;
    color: #333;
}

table tr:nth-child(even) {
    background-color: #fff0f5;
}

/* Pagination */
.pagination {
    justify-content: center;
    margin-top: 15px;
}

.page-link {
    color: #ff66b2;
}

.page-item.active .page-link {
    background-color: #ff66b2;
    border-color: #ff66b2;
}

.page-item.disabled .page-link {
    color: #ff99cc;
    background-color: #ffe6f2;
}
</style>
