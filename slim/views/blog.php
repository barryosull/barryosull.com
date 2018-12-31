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

    <section class="blog">
        <h1>Blog <small>Articles on DDD, Event Sourcing and software development in general, with a sprinkle of PHP and sarcasm.</small></h1>

        <?php if (empty($articles)): ?>
            <p>Sorry - no articles found.</p>
        <?php else: ?>

            <div style="float:left; width:25%" class="categories">
                <div class="hl-sm">Topics</div>
                <?php foreach ($categories as $category): ?>
                    <a href="<?php echo "/blog/category/".Nekudo\ShinyBlog\Domain\SlugFactory::makeSlug($category); ?>" class="btn"><?php echo $category; ?></a><br>
                <?php endforeach; ?>
            </div>

            <div style="float:left; width:75%">
                <?php foreach ($articles as $article): ?>
                    <article class="excerpt">
                        <header>
                            <h2><a href="<?php echo $article->url; ?>"><?php echo $article->title; ?></a></h2>
                            <div class="article-meta">
                                <?php if ($article->date != ''):?>
                                    published at
                                    <time datetime="<?php echo $article->date; ?>" pubdate>
                                        <?php echo $article->date; ?>
                                    </time>
                                <?php endif; ?>
                            </div>
                        </header>
                        <blockquote class="article-excerpt">
                            <?php echo $article->excerpt; ?>
                        </blockquote>
                    </article>
                <?php endforeach; ?>
            </div>
            <div style="clear:both"></div>
        <?php endif; ?>

        <?php if (!empty($urlPrevPage) || !empty($urlNextPage)): ?>
            <nav class="pagination">
                <?php if (!empty($urlPrevPage)): ?>
                    <a class="previous-page" href="<?php echo $urlPrevPage; ?>">
                        &laquo; Previous Page
                    </a>
                <?php endif; ?>
                <?php if (!empty($urlNextPage)): ?>
                    <a class="next-page" href="<?php echo $urlNextPage; ?>">
                        Next Page &raquo;
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>

    </section>

</div>
<div class="footer">
    <p>barryosull.com &copy; 2018</p>
</div>
<script src="/themes/kiss/js/prism.js"></script>
</body>
</html>
