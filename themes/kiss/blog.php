
<section class="blog">
    <?php if ($showTitle === true): ?>
        <h1>Blog <small>Articles on DDD, Event Sourcing and software development in general, with a sprinkle of PHP and sarcasm.</small></h1>
    <?php endif; ?>
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
                        <h2><a href="<?php echo $article->getUrl(); ?>"><?php echo $article->getTitle(); ?></a></h2>
                        <div class="article-meta">
                            <?php if ($article->getDate() != ''):?>
                                published at
                                <time datetime="<?php echo $article->getDate(); ?>" pubdate>
                                    <?php echo $article->getDate(); ?>
                                </time>
                            <?php endif; ?>
                        </div>
                    </header>
                    <blockquote class="article-excerpt">
                        <?php echo $article->getExcerpt(true); ?>
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
