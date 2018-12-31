<!DOCTYPE html>
<html lang="en">
<head>
    <?php if (getenv('ENV') != "development") :?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-112076964-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-112076964-1');
        </script>
    <?php endif; ?>
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

    <meta charset="utf-8">
    <title><?php echo $page->title; ?></title>
    <meta name="robots" content="index,follow" />
    <meta name="description" content="<?php echo $page->description; ?>">
    <link rel="stylesheet" href="/themes/kiss/css/kiss.css">
    <link rel="stylesheet" href="/themes/kiss/css/prism.css">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <?php if (isset($article)) :?>
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="@barryosull" />
        <meta name="twitter:title" content="<?php echo $article->title ?>" />
        <meta name="twitter:description" content="<?php echo $article->description ?>" />
        <meta name="twitter:image" content="<?php echo $article->coverImage ?>" />

        <meta property="og:title" content="<?php echo $article->title ?>"/>
        <meta property="og:image" content="<?php echo $article->coverImage ?>" />
        <meta property="og:description" content="<?php echo $article->description ?>" />
    <?php endif; ?>

</head>
<body>
<nav class="topnav">
    <ul>
        <li class="first"><a href="/">barryosull.com</a></li>
        <li<?php if($_SERVER['REQUEST_URI'] !== '/'): ?> class="active"<?php endif; ?>><a href="/blog">Blog</a></li>
        <li><a href="https://www.slideshare.net/BarryOSullivan18/">Talks</a></li>
        <li><a href="https://github.com/barryosull">Github</a></li>
        <li class="last"><a href="https://twitter.com/barryosull">Twitter</a></li>
        <li><a href="https://www.linkedin.com/in/barryosu/">LinkedIn</a></li>
    </ul>
</nav>
<div class="content">
    <article class="page">
        <?php if(!empty($page->title)): ?>
            <h1><?php echo $page->title; ?></h1>
        <?php endif; ?>
        <div class="page-content">
            <?php echo (new Parsedown)->parse($page->content); ?>
        </div>
    </article>

</div>
<div class="footer">
    <p>barryosull.com &copy; 2018</p>
</div>
<script src="/themes/kiss/js/prism.js"></script>
</body>
</html>