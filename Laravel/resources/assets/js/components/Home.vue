<template>
    <article class="page">
        <div class="page-content">
            <vue-markdown :source="content"></vue-markdown>
        </div>
    </article>
</template>

<script>

const axios = require('axios');

export default {
    data() {
        return {
            content: "Waiting for content"
        };
    },
    mounted () {
        axios
            .get('/api/page/home')
            .then(response => (this.content = response.data.content))
    }
};

</script>