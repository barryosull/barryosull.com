<?php if (!$article->getPublished()) :?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
    <script src="http://assets.annotateit.org/annotator/v1.1.0/annotator-full.min.js"></script>
    <link rel="stylesheet" href="http://assets.annotateit.org/annotator/v1.1.0/annotator.min.css">

    <script type="text/javascript">
        jQuery(function ($) {
            $('.article').annotator();
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
