<?php

namespace Tbb\Blog\Repository;

use Tbb\Blog\Api\Data\PostInterface;
use Tbb\Blog\Api\Repository\PostRepositoryInterface;
use Tbb\Blog\Model\Post;
use Tbb\Blog\Model\ResourceModel\Post\CollectionFactory;
use Tbb\Blog\Api\Data\PostInterfaceFactory;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Exception\InputException;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @var PostInterfaceFactory
     */
    private $factory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var CatRepositoryInterface
     */
    private $catRepository;

    /**
     * @var FilterManager
     */
    private $filterManager;

    public function __construct(
        PostInterfaceFactory $factory,
        CollectionFactory $collectionFactory,

        FilterManager $filterManager
    ) {
        $this->factory           = $factory;
        $this->collectionFactory = $collectionFactory;

        $this->filterManager     = $filterManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }


    /**
     * @inheritdoc
     */
    public function getList()
    {
        /** @var \Tbb\Blog\Model\ResourceModel\Post\Collection $collection */
        $collection = $this->getCollection();
        return $collection->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        /** @var Post $post */
        $post = $this->create();

        $post->getResource()->load($post, $id);

        if ($post->getId()) {
            return $post;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, PostInterface $post)
    {
        /** @var Post $model */
        $model = $this->create();
        $model->getResource()->load($model, $id);

        if (!$model->getId()) {
            throw new InputException(__("The post doesn't exist."));
        }

        $json = json_decode(file_get_contents("php://input"));

        foreach ($json->post as $k => $v) {
            $model->setData($k, $post->getData($k));
        }

        $model->getResource()->save($model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function apiDelete($id)
    {
        /** @var Post $post */
        $post = $this->create();
        $post->getResource()->load($post, $id);

        if (!$post->getId()) {
            throw new InputException(__("The post doesn't exist."));
        }

        $post->getResource()->delete($post);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(PostInterface $model)
    {
        /** @var Post $model */
        return $model->getResource()->delete($model);
    }

    /**
     * {@inheritdoc}
     */
    public function save(PostInterface $model)
    {
        if (!$model->getType()) {
            $model->setType(PostInterface::TYPE_POST);
        }
        if (!$model->getUrlKey()) {
            $model->setUrlKey($this->filterManager->translitUrl($model->getName()));
        }


        /** @var Post $model */
        $model->getResource()->save($model);

        return $model;
    }

}