<?php

namespace Tbb\Blog\Model\ResourceModel\Post;

use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Tbb\Blog\Api\Data\PostInterface;
use Tbb\Blog\Model\Post;
use Tbb\Blog\Model\Post\Attribute\Source\Status;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Tbb\Blog\Model\Post', 'Tbb\Blog\Model\ResourceModel\Post');
    }

    public function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $item->load($item->getId());
        }

        return parent::_afterLoad();
    }

    /**
     * @return $this
     */
    public function addVisibilityFilter()
    {
        $this->addAttributeToFilter(PostInterface::STATUS, PostInterface::STATUS_PUBLISHED);
        $this->addFieldToFilter(PostInterface::TYPE, PostInterface::TYPE_POST);

        return $this;
    }


    /**
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        // NOT EXISTS is compatibility for prev versions
        $this->getSelect();

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function addRelatedProductFilter($product)
    {
        /*$this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('mst_blog_post_product')}`
                AS `post_product`
                WHERE e.entity_id = post_product.post_id
                AND post_product.product_id in (?))", [0, $product->getId()]);

        return $this;*/
    }


    /**
     * @param string $q
     * @return $this
     */
    public function addSearchFilter($q)
    {
        $likeExpression = $this->_resourceHelper->addLikeEscape($q, ['position' => 'any']);

        $this->addAttributeToSelect('name');
        $this->addAttributeToSelect(['content', 'short_content'], 'left');

        $this->addAttributeToFilter([
            ['attribute' => 'name', 'like' => $likeExpression],
            ['attribute' => 'content', 'like' => $likeExpression],
            ['attribute' => 'short_content', 'like' => $likeExpression],
        ]);

        return $this;
    }


    /**
     * @return $this
     */
    public function addPostFilter()
    {
        $this->addFieldToFilter('type', \Tbb\Blog\Model\Post::TYPE_POST);

        return $this;
    }

    /**
     * @return $this
     */
    public function defaultOrder()
    {
        $this->addAttributeToSort('is_pinned', self::SORT_ORDER_DESC);
        $this->getSelect()->order('updated_at DESC');

        return $this;
    }
}