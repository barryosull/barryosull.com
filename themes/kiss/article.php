<?php if (!$article->getPublished()) :?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
    <script src="http://assets.annotateit.org/annotator/v1.1.0/annotator-full.min.js"></script>
    <link rel="stylesheet" href="http://assets.annotateit.org/annotator/v1.1.0/annotator.min.css">

    <script type="text/javascript">

        var articleSlug = "<?=$article->getSlug()?>";

        jQuery(function ($) {
            var annotator = $('.article').annotator();

            annotator.annotator('addPlugin', 'Store', {
                prefix: '/api/annotator/'+articleSlug,
            });
        });


    </script>

<?php endif; ?>

<article class="article">
    <header>
        <?php if ($article->getCoverImage()):?>
            <div class="image image-final" style="background-color:#e3dac6;background-image:url(<?php echo $article->getCoverImage(); ?>)"></div>
        <?php endif; ?>

        <h1><?php echo $article->getTitle(); ?></h1>
        <span style="float:right" class="article-meta">
            published on
            <time datetime="<?php echo $article->getDate(); ?>" pubdate>
                <?php echo $article->getDate(); ?>
            </time>
            by <?php echo $article->getAuthor(); ?>
        </span>

        <a href="https://twitter.com/intent/tweet?text=<?php echo $article->getTitle()?>&url=http%3A%2F%2Fbarryosull.com%2Fblog%2F<?php echo $article->getSlug()?>&via=barryosull" class="twitter-share-button" data-size="large" data-show-count="false">Tweet</a>

    </header>
    <div class="entry-content">
        <?php echo (new ParsedownExtra)->parse($article->getContent()); ?>

        <a href="https://twitter.com/intent/tweet?text=<?php echo $article->getTitle()?>&url=http%3A%2F%2Fbarryosull.com%2Fblog%2F<?php echo $article->getSlug()?>&via=barryosull" class="twitter-share-button" data-size="large" data-show-count="false">Tweet</a>

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
        <?php if ($article->hasCategories()): ?>
            <div class="categories">
            <div class="hl-sm">Categories</div>
            <?php foreach ($article->getCategories() as $category): ?>
                <a href="<?php echo $category['link']; ?>" class="btn"><?php echo $category['name']; ?></a>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </footer>
</article>
