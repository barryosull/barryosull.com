<template>
    <article class="page">
        <div class="page-content" v-html="content"></div>
    </article>
</template>

<script>

const axios = require('axios');

export default {
    data() {
        return {
            content: "Waiting for page"
        };
    },
    mounted () {
        axios
            .get('/api/page/home')
            .then(response => (this.content = response.data.content))
    }
};

</script>