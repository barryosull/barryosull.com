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
        <title><?php echo $this->getTitle(); ?></title>
        <meta name="robots" content="<?php echo $this->getIndex(); ?>" />
        <meta name="description" content="<?php echo $this->getDescription(); ?>">
        <?php if (!empty($this->getFeedUrl())): ?>
            <link rel="alternate" type="application/rss+xml" href="<?php echo $this->getFeedUrl(); ?>">
        <?php endif; ?>
        <link rel="stylesheet" href="/themes/kiss/css/kiss.css">
        <link rel="stylesheet" href="/themes/kiss/css/prism.css">
    </head>
    <body>
        <nav class="topnav">
            <ul>
                <li class="first"><a href="/">barryosull.com</a></li>
                <li<?php if($navActive === 'blog'): ?> class="active"<?php endif; ?>><a href="/blog">Blog</a></li>
                <li<?php if($navActive === 'talks'): ?> class="active"<?php endif; ?>><a href="/talks">Talks</a></li>
                <li><a href="https://github.com/barryosull">Github</a></li>
                <li class="last"><a href="https://twitter.com/barryosull">Twitter</a></li>
                <li><a href="https://www.linkedin.com/in/barryosu/">LinkedIn</a></li>
            </ul>
        </nav>
        <div class="content">
            <?php echo $template; ?>
        </div>
        <div class="footer">
            <p>barryosull.com - because I need a blog and this will do</p>
        </div>
        <script src="/themes/kiss/js/prism.js"></script>
    </body>
</html>
