<? echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>
<rss version="2.0">
    <channel>
        <title>Blog - Barry O Sullivan</title>
        <link><?php echo $root ?>/blog/feed</link>
        <description>Articles on DDD, Event Sourcing and software development in general, with a sprinkle of PHP and sarcasm.</description>
        <pubDate><?php echo $pubDate ?></pubDate>
<? foreach ($articles as $article): ?>
        <item>
            <title>Why I don't like traits</title>
            <link><?php echo $root . $article->url ?></link>
            <guid><?php echo $root . $article->url ?></guid>
            <pubDate><?php echo date(DATE_RSS, strtotime($article->date)) ?></pubDate>
            <description><![CDATA[<?php echo $article->content ?>]]></description>
        </item>
<?php endforeach ?>
    </channel>
</rss>