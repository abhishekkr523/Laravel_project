

<template>
  <!-- <div class="overflow-auto"> -->
    <!-- Pagination Component -->
    <b-pagination
      v-model="currentPage"
      :total-rows="rows"
      :per-page="perPage"
      aria-controls="my-table"
    ></b-pagination>

    <!-- Current Page Indicator -->
    <p class="mt-3">Current Page: {{ currentPage }}</p>

    <!-- Table Component -->
    <b-table
      id="my-table"
      :items="paginatedItems"
      small
    ></b-table>
  <!-- </div> -->
</template>

<script>
  export default {
    data() {
      return {
        perPage: 3, // Number of items per page
        currentPage: 1, // Default to first page
        items: [
          { id: 1, first_name: 'Fred', last_name: 'Flintstone' },
          { id: 2, first_name: 'Wilma', last_name: 'Flintstone' },
          { id: 3, first_name: 'Barney', last_name: 'Rubble' },
          { id: 4, first_name: 'Betty', last_name: 'Rubble' },
          { id: 5, first_name: 'Pebbles', last_name: 'Flintstone' },
          { id: 6, first_name: 'Bamm Bamm', last_name: 'Rubble' },
          { id: 7, first_name: 'The Great', last_name: 'Gazzoo' },
          { id: 8, first_name: 'Rockhead', last_name: 'Slate' },
          { id: 9, first_name: 'Pearl', last_name: 'Slaghoople' }
        ]
      }
    },
    computed: {
      // Calculate the total number of rows
      rows() {
        return this.items.length;
      },
      // Compute the items to display for the current page
      paginatedItems() {
        const start = (this.currentPage - 1) * this.perPage;
        const end = start + this.perPage;
        return this.items.slice(start, end);
      }
    }
  }
</script>
