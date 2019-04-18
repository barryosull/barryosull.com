<nav class="topnav">
    <ul>
        <li class="first"><a href="/">barryosull.com</a></li>
        <li<?php if(strpos($_SERVER['REQUEST_URI'], '/blog') !== false) : ?> class="active"<?php endif; ?>><a href="/blog">Blog</a></li>
        <li<?php if(strpos($_SERVER['REQUEST_URI'], '/talks') !== false): ?> class="active"<?php endif; ?>><a href="/talks">Talks</a></li>
        <li><a href="https://github.com/barryosull">Github</a></li>
        <li class="last"><a href="https://twitter.com/barryosull">Twitter</a></li>
        <li><a href="https://www.linkedin.com/in/barryosu/">LinkedIn</a></li>
    </ul>
</nav>