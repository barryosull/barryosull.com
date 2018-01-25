<?php
declare(strict_types=1);
namespace Nekudo\ShinyBlog\Controller\Http;

use Nekudo\ShinyBlog\Domain\ShowBlogDomain;

class ShowBlogUnpublished extends BaseAction
{
    protected $domain;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->domain = new ShowBlogDomain($this->config);
    }

    /**
     * Renders requested article and sends it to client.
     *
     * @param array $arguments
     */
    public function __invoke(array $arguments)
    {
        $not_published = $this->domain->getUnpublishedArticles();

        echo "<ol>";
        foreach ($not_published as $article) {
            echo '<li><a style="font-family: Arial, Helvetica, sans-serif;" href="'.$article->getUrl().'">'.$article->getTitle()."</a></li>";
        }
        echo "</ol>";
    }
}
