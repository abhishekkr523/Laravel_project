<template>
    <div class="home-container">
        <h1>Welcome to the Home Page</h1>
        <p>This is the home component content.</p>

        <h1>{{ data }}</h1>
    </div>
</template>

<script>
export default {
    name: "Home",
    props: {
        data: String,
    },
};
</script>

<style scoped>
.home-container {
    text-align: center;
    margin-top: 20px;
}
</style>
