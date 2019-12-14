<?php

namespace Tbb\Blog\Api\Repository;

interface PostRepositoryInterface
{
    /**
     * @return \Tbb\Blog\Model\ResourceModel\Post\Collection | \Tbb\Blog\Api\Data\PostInterface[]
     */
    public function getCollection();

    /**
     * @return \Tbb\Blog\Api\Data\PostInterface
     */
    public function create();

    /**
     * @param \Tbb\Blog\Api\Data\PostInterface $model
     * @return \Tbb\Blog\Api\Data\PostInterface
     */
    public function save(\Tbb\Blog\Api\Data\PostInterface $model);

    /**
     * @return \Tbb\Blog\Api\Data\PostInterface[]
     */
    public function getList();

    /**
     * @param int $id
     * @return \Tbb\Blog\Api\Data\PostInterface|false
     */
    public function get($id);

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function apiDelete($id);
    /**
     * @param int $id
     * @param \Tbb\Blog\Api\Data\PostInterface $post
     * @return \Tbb\Blog\Api\Data\PostInterface
     * @throws \Magento\Framework\Exception\StateException
     */
    public function update($id, \Tbb\Blog\Api\Data\PostInterface $post);

    /**
     * @param \Tbb\Blog\Api\Data\PostInterface $model
     * @return bool
     */
    public function delete(\Tbb\Blog\Api\Data\PostInterface $model);
}