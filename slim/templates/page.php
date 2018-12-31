<article class="page">
    <?php if(!empty($page->getTitle())): ?>
        <h1><?php echo $page->getTitle(); ?></h1>
    <?php endif; ?>
    <div class="page-content">
        <?php echo (new Parsedown)->parse($page->getContent()); ?>
    </div>
</article>
