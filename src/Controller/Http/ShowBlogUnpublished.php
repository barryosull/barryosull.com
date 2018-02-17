<?php namespace Nekudo\ShinyBlog\Controller\Http;

use Nekudo\ShinyBlog\Domain\ShowBlogDomain;
use Nekudo\ShinyBlog\Responder\HttpResponder;

class ShowBlogUnpublished extends BaseAction
{
    protected $domain;
    private $responder;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->domain = new ShowBlogDomain($this->config);
        $this->responder = new HttpResponder($this->config);
    }

    public function __invoke(array $arguments)
    {
        $not_published = $this->domain->getUnpublishedArticles();
        $response = "<h1 style=\"font-family: Arial, Helvetica, sans-serif;\">The Unpublished Ones</h1>";
        $response .= "<ol>";
        foreach ($not_published as $article) {
            $response .= '<li><a style="font-family: Arial, Helvetica, sans-serif;" href="'.$article->getUrl().'">'.$article->getTitle()."</a></li>";
        }
        $response .= "</ol>";

        $this->responder->found($response);

        return $this->responder;
    }
}
