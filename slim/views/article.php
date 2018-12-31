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

    <article class="article">
        <header>
            <?php if ($article->coverImage):?>
                <div class="image image-final" style="background-color:#e3dac6;background-image:url(<?php echo $article->coverImage; ?>)"></div>
            <?php endif; ?>

            <h1><?php echo $article->title; ?></h1>
            <span style="float:right" class="article-meta">
            published on
            <time datetime="<?php echo $article->date; ?>" pubdate>
                <?php echo $article->date; ?>
            </time>
            by <?php echo $article->author; ?>
        </span>

            <a href="https://twitter.com/intent/tweet?text=<?php echo $article->title?>&url=http%3A%2F%2Fbarryosull.com%2Fblog%2F<?php echo $article->slug?>&via=barryosull" class="twitter-share-button" data-size="large" data-show-count="false">Tweet</a>

        </header>
        <div class="entry-content">
            <?php echo (new ParsedownExtra)->parse($article->content); ?>

            <a href="https://twitter.com/intent/tweet?text=<?php echo $article->title?>&url=http%3A%2F%2Fbarryosull.com%2Fblog%2F<?php echo $article->slug?>&via=barryosull" class="twitter-share-button" data-size="large" data-show-count="false">Tweet</a>

        </div>
        <div id="mc_embed_signup">
            <form action="https://barryosull.us17.list-manage.com/subscribe/post?u=9b492ce0918014d517e6f5985&amp;id=6f3befd048" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                <div id="mc_embed_signup_scroll">
                    <h2>Subscribe for more content like this</h2>
                    <div class="mc-field-group">
                        <label for="mce-EMAIL">Email Address
                        </label>
                        <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
                    </div>
                    <div id="mce-responses" class="clear">
                        <div class="response" id="mce-error-response" style="display:none"></div>
                        <div class="response" id="mce-success-response" style="display:none"></div>
                    </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_9b492ce0918014d517e6f5985_6f3befd048" tabindex="-1" value=""></div>
                    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                </div>
            </form>

            <link href="/mailchimp.css" rel="stylesheet" type="text/css">
            <script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[3]='ADDRESS';ftypes[3]='address';fnames[4]='PHONE';ftypes[4]='phone';fnames[5]='BIRTHDAY';ftypes[5]='birthday';}(jQuery));var $mcj = jQuery.noConflict(true);</script>

        </div>
        <footer>
            <?php if (count($article->categories) != 0): ?>
                <div class="categories">
                    <div class="hl-sm">Categories</div>
                    <?php foreach ($article->categories as $category): ?>
                        <a href="<?php echo $category['link']; ?>" class="btn"><?php echo $category['name']; ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <br>
            <div id="disqus_thread"></div>
            <script>
                var disqus_config = function () {
                    this.page.identifier = "<?php echo $article->slug ?>";
                };
                (function() { // DON'T EDIT BELOW THIS LINE
                    var d = document, s = d.createElement('script');
                    s.src = 'https://barry-o-sullivans-development-services.disqus.com/embed.js';
                    s.setAttribute('data-timestamp', +new Date());
                    (d.head || d.body).appendChild(s);
                })();
            </script>
            <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>

        </footer>

    </article>

</div>
<div class="footer">
    <p>barryosull.com &copy; 2018</p>
</div>
<script src="/themes/kiss/js/prism.js"></script>
</body>
</html>
