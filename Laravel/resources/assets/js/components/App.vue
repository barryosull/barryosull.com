<template>
    <div>
        <template>
            <nav class="topnav">
                <ul>
                    <li><router-link to="/">barryosull.com</router-link></li>
                    <li><router-link to="/blog">Blog</router-link></li>
                    <li><a href="https://www.slideshare.net/BarryOSullivan18/">Talks</a></li>
                    <li><a href="https://github.com/barryosull">Github</a></li>
                    <li class="last"><a href="https://twitter.com/barryosull">Twitter</a></li>
                    <li><a href="https://www.linkedin.com/in/barryosu/">LinkedIn</a></li>
                </ul>
            </nav>
        </template>

        <div class="content">
            <router-view></router-view>
        </div>
        <div class="footer">
            <p>barryosull.com &copy; 2018</p>
        </div>
    </div>
</template>