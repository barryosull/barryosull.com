<article class="article">
    <header>
        <?php if ($article->getCoverImage()):?>
            <div class="image image-final" style="background-color:#e3dac6;background-image:url(<?php echo $article->getCoverImage(); ?>)"></div>
        <?php endif; ?>

        <h1><?php echo $article->getTitle(); ?></h1>
        <div class="article-meta">
            published at
            <time datetime="<?php echo $article->getDate(); ?>" pubdate>
                <?php echo $article->getDate(); ?>
            </time>
            by <?php echo $article->getAuthor(); ?>
        </div>
    </header>
    <div class="entry-content">
        <?php echo $article->getContent(); ?>
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
