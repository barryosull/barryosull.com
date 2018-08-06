<template>
    <nav class="topnav">
        <ul>
            <li class="first"><a href="/">barryosull.com</a></li>
            <li><a href="/blog">Blog</a></li>
            <li><a href="https://www.slideshare.net/BarryOSullivan18/">Talks</a></li>
            <li><a href="https://github.com/barryosull">Github</a></li>
            <li class="last"><a href="https://twitter.com/barryosull">Twitter</a></li>
            <li><a href="https://www.linkedin.com/in/barryosu/">LinkedIn</a></li>
        </ul>
    </nav>
</template>
<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data: function () {
            return {
                count: 0
            }
        },
        //template: '<button v-on:click="count++">You clicked me {{ count }} times.</button>'
    }
</script>
