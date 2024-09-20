<template>
    <div>
        <div class="up">
            <form @submit.prevent="uploadFile">
                <input type="file" ref="fileInput" name="file" />
                <button type="submit">Import Products</button>
            </form>
            <div v-if="message">{{ message }}</div>
        </div>
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
            <tfoot>
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
            </tfoot>
        </table>

        <div>
            <div class="overflow-auto">
                <!-- Pagination Component -->
                <b-pagination
                    v-model="currentPage"
                    :total-rows="rows"
                    :per-page="perPage"
                    aria-controls="example"
                    @change="changePage"
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
            laravelData: {
                current_page: 1,
                data: [],
                last_page: 1,
                total: 0,
            },
        };
    },
    computed: {
        // Total number of rows based on API response
        rows() {
            return this.laravelData.total;
        },
        // Items per page
        perPage() {
            return Number(this.pageLimit);
        },
    },
    watch: {
        pageLimit(newLimit) {
            if (this.table) {
                this.table.page.len(newLimit).draw();
                this.loadPageData(1); // Reload data from the first page
            }
        },
    },
    methods: {
        toggleFilterForm() {
            this.showFilterForm = !this.showFilterForm; // Toggle form visibility
        },

        resetFilters() {
            this.selectedCategory = "";
            this.selectedOrder = "";
            this.selectedCondition = "";
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

        changePage(page) {
            console.log("Page changed to:", page);
            this.currentPage = page;
            this.getResults(page); // Fetch new data based on the page number
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
                let params = {};
                params.page = 1;
                params.limit = this.pageLimit;
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

                this.table.ajax.url(apiUrl).load();
            } else {
                console.error("DataTable instance is not initialized.");
            }

            this.showFilterForm = false;
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
    },
};
</script>

<style scoped>
/* Your custom CSS styling */
</style>

<style>
.top {
    display: flex !important;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.bottom {
    display: flex;
    justify-content: space-between;
}
.up {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    margin: 20px auto;
    max-width: 600px;
    padding: 10px;
    border-radius: 12px;
    background: #e0e0e0; /* Light grey background */
    box-shadow: 8px 8px 16px #bcbcbc, -8px -8px 16px #ffffff; /* Neumorphism shadow */
}

/* Form styling */
.up form {
    display: flex;
    align-items: center;
    flex: 1;
}

/* File input styling */
.up input[type="file"] {
    margin-right: 10px;
    padding: 10px;
    border: none;
    border-radius: 8px;
    background: #e0e0e0;
    box-shadow: inset 4px 4px 8px #bcbcbc, inset -4px -4px 8px #ffffff; /* Neumorphism shadow */
    font-size: 14px;
    flex: 1;
}

/* Button styling */
.up button {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    background: #e0e0e0;
    color: #333;
    font-size: 14px;
    cursor: pointer;
    box-shadow: 4px 4px 8px #bcbcbc, -4px -4px 8px #ffffff; /* Neumorphism shadow */
    transition: background 0.3s, transform 0.2s;
}

.up button:hover {
    background: #d0d0d0; /* Slightly darker on hover */
    transform: scale(1.02); /* Slight zoom effect on hover */
}

/* Message styling */
.up div[v-if="message"] {
    margin-left: 15px;
    padding: 8px 12px;
    border-radius: 8px;
    background: #e0e0e0;
    color: #333;
    border: 1px solid #c0c0c0;
    box-shadow: inset 4px 4px 8px #bcbcbc, inset -4px -4px 8px #ffffff; /* Neumorphism shadow */
}

/* Table styling */
#example {
    width: 100%;
    border-collapse: collapse;
    border-radius: 12px;
    background: #e0e0e0; /* Light grey background */
    box-shadow: 8px 8px 16px #bcbcbc, -8px -8px 16px #ffffff; /* Neumorphism shadow */
}

/* Table header */
#example thead {
    background: #d0d0d0; /* Slightly darker grey */
}

#example th {
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #b0b0b0;
    color: #333;
    border-radius: 8px 8px 0 0; /* Rounded top corners */
}

/* Table body rows */
#example tbody tr {
    border-bottom: 2px solid #b0b0b0;
}

#example td {
    padding: 12px;
    border-right: 1px solid #d0d0d0;
}

/* Alternating row colors */
#example tbody tr:nth-child(odd) {
    background: #f0f0f0;
}

#example tbody tr:nth-child(even) {
    background: #e0e0e0;
}

/* Hover effect on rows */
#example tbody tr:hover {
    background: #d8d8d8;
}

/* Style for the image column */
#example tbody td img {
    max-width: 100px;
    height: auto;
    display: block;
    border-radius: 8px;
}

.filter_data {
    padding: 1rem;
}

.filter-container {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
}

.filter-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.custom-search {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 200px;
    box-sizing: border-box;
}

.search-icon {
    font-size: 1rem;
    color: #666;
}

.page-limit-select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100px;
}

.page-limit-icon {
    font-size: 1rem;
    color: #666;
}

.filter-btn {
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 0.5rem 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-btn:hover {
    background-color: #0056b3;
}

.filter-icon {
    font-size: 1rem;
}

.filter-form {
    margin-top: 1rem;
    display: flex;
}

.form-group {
    margin-bottom: 1rem;
}

.select-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.select-filter {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 200px;
}

.category-icon {
    font-size: 1rem;
    color: #666;
}

.order-icon {
    font-size: 1rem;
    color: #666;
}

.condition-icon {
    font-size: 1rem;
    color: #666;
}

#custom-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.nav-btn {
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 0.5rem 1rem;
    cursor: pointer;
    font-size: 1rem;
}

.nav-btn:hover {
    background-color: #0056b3;
}

#page-info {
    font-size: 1rem;
}

.page-input {
    width: 50px;
    padding: 0.25rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    text-align: center;
}
</style>
