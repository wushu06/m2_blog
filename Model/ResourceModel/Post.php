<?php

namespace Tbb\Blog\Model\ResourceModel;

use Magento\Framework\DataObject;
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Context;
use Tbb\Blog\Api\Data\PostInterface;
use Tbb\Blog\Model\Config;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\File\Uploader as FileUploader;

class Post extends AbstractEntity
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FilterManager
     */
    protected $filter;



    public function __construct(
        Config $config,
        FilterManager $filter,
        Context $context,
        $data = []
    ) {
        $this->config = $config;
        $this->filter = $filter;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\Tbb\Blog\Model\Post::ENTITY);
        }

        return parent::getEntityType();
    }

    protected function _afterLoad(DataObject $post)
    {
        /** @var PostInterface $post */

        $post->setProductIds($this->getProductIds($post));

        return parent::_afterLoad($post);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(DataObject $post)
    {
        /** @var PostInterface $post */

        $this->saveProductIds($post);

        return parent::_afterSave($post);
    }

    /**
     * @param PostInterface $model
     * @return array
     */
    private function getCategoryIds(PostInterface $model)
    {

    }

    /**
     * @param PostInterface $model
     * @return $this
     */
    private function saveCategoryIds(PostInterface $model)
    {

    }

    /**
     * @param PostInterface $model
     * @return array
     */
    private function getStoreIds(PostInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('mst_blog_store_post'),
            'store_id'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PostInterface $model
     * @return $this
     */
    private function saveStoreIds(PostInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('mst_blog_store_post');

        /**
         * If store ids data is not declared we haven't do manipulations
         */
        if (!$model->getStoreIds()) {
            return $this;
        }

        $storeIds = $model->getStoreIds();
        $oldStoreIds = $this->getStoreIds($model);

        $insert = array_diff($storeIds, $oldStoreIds);
        $delete = array_diff($oldStoreIds, $storeIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $storeId) {
                if (empty($storeId)) {
                    continue;
                }
                $data[] = [
                    'store_id' => (int)$storeId,
                    'post_id'  => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $storeId) {
                $where = ['post_id = ?' => (int)$model->getId(), 'store_id = ?' => (int)$storeId];
                $connection->delete($table, $where);
            }
        }

        return $this;
    }

    /**
     * @param PostInterface $model
     * @return array
     */
    private function getTagIds(PostInterface $model)
    {

    }

    /**
     * @param PostInterface $model
     * @return $this
     */
    private function saveTagIds(PostInterface $model)
    {

    }

    /**
     * @param PostInterface $model
     * @return array
     */
    private function getProductIds(PostInterface $model)
    {
        return [];
        /*$connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('mst_blog_post_product'),
            'product_id'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);*/
    }

    /**
     * @param PostInterface $model
     * @return $this
     */
    private function saveProductIds(PostInterface $model)
    {
        return [];
        $connection = $this->getConnection();

        $table = $this->getTable('mst_blog_post_product');

        if (!$model->getProductIds()) {
            return $this;
        }

        $productIds = $model->getProductIds();
        $oldProductIds = $this->getProductIds($model);

        $insert = array_diff($productIds, $oldProductIds);
        $delete = array_diff($oldProductIds, $productIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $productId) {
                if (empty($productId)) {
                    continue;
                }
                $data[] = [
                    'product_id' => (int)$productId,
                    'post_id'    => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $productId) {
                $where = ['post_id = ?' => (int)$model->getId(), 'product_id = ?' => (int)$productId];
                $connection->delete($table, $where);
            }
        }

        return $this;
    }

}