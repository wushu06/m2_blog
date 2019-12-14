<?php

namespace Tbb\Blog\Block\Post;

use Magento\Framework\DataObject\IdentityInterface;
use Tbb\Blog\Api\Repository\PostRepositoryInterface;
use Tbb\Blog\Model\Config;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Tbb\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;

class PostList extends AbstractBlock implements IdentityInterface
{
    /**
     * @var string
     */
    protected $defaultToolbarBlock = 'Tbb\Blog\Block\Post\PostList\Toolbar';

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \Tbb\Blog\Model\ResourceModel\Post\Collection
     */
    protected $collection;

    public function __construct(
        PostRepositoryInterface $postRepository,
        Config $config,
        Registry $registry,
        Context $context
    ) {
        $this->postRepository = $postRepository;

        parent::__construct($config, $registry, $context);
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getPostCollection();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }

        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }

        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }

        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);

        $this->setCollection($toolbar->getCollection());

        $this->getPostCollection()->load();

        return parent::_beforeToHtml();
    }

    /**
     * @return PostList\Toolbar
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();

        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }

        $block = $this->getLayout()->createBlock($this->defaultToolbarBlock, uniqid(microtime()));

        return $block;
    }

    /**
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * @param \Tbb\Blog\Model\ResourceModel\Post\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Return identifiers for post content.
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];

        foreach ($this->getPostCollection() as $post) {
            $identities = array_merge($identities, $post->getIdentities());
        }

        return $identities;
    }

    /**
     * Retrieve current category model object.
     *
     * @return \Tbb\Blog\Model\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_blog_category');
    }

    /**
     * @return \Tbb\Blog\Model\Tag
     */
    public function getTag()
    {
        return $this->registry->registry('current_blog_tag');
    }

    /**
     * @return \Tbb\Blog\Model\Author
     */
    public function getAuthor()
    {
        return $this->registry->registry('current_blog_author');
    }

    /**
     * @return string
     */
    public function getSearchQuery()
    {
        return $this->registry->registry('current_blog_query');
    }

    /**
     * @return \Tbb\Blog\Model\ResourceModel\Post\Collection
     */
    public function getPostCollection()
    {

        $toolbar = $this->getToolbarBlock();

        if (empty($this->collection)) {
            $collection = $this->postRepository->getCollection()
                ->addAttributeToSelect([
                    'name', 'featured_image', 'featured_alt', 'featured_show_on_home',
                    'short_content', 'content', 'url_key',
                ])
                ->addStoreFilter($this->context->getStoreManager()->getStore()->getId())
                ->addVisibilityFilter();




            $collection->setCurPage($this->getCurrentPage());

            $limit = (int)$toolbar->getLimit();
            if ($limit) {
                $collection->setPageSize($limit);
            }

            $page = (int)$toolbar->getCurrentPage();
            if ($page) {
                $collection->setCurPage($page);
            }

            if ($order = $toolbar->getCurrentOrder()) {
                $collection->setOrder($order, $toolbar->getCurrentDirection());
            }
            $collection->defaultOrder();

            $this->collection = $collection;

        }

        return $this->collection;
    }

    /**
     * @param \Tbb\Blog\Model\Post $post
     * @return string
     */
    public function getFeaturedAlt($post)
    {
        return $post->getFeaturedAlt() ?: $post->getName();
    }

    /**
     * @param \Tbb\Blog\Model\Post $post
     * @return string
     */
    public function getContentMoreTag($post)
    {
        if ($this->config->getExcerptsEnabled()) {
            $size = $this->config->getExcerptSize();
            if ($exerpt = strpos($post->getContent(), '<!--more-->')) {
                return substr($post->getContent(), 0, $exerpt);
            } elseif ($post->getShortContent()) {
                return $post->getShortContent();
            } elseif (preg_match('/^.{1,' . $size . '}\b/s', $this->stripTags(
                preg_replace("/<style\\b[^>]*>(.*?)<\\/style>/s", "",$post->getContent())
            ), $match)) {
                return $match[0];
            }
            return $post->getContent();
        }
        return '';
    }
}
