<?php

namespace Tbb\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Tbb\Blog\Api\Repository\PostRepositoryInterface;
use Tbb\Blog\Controller\Adminhtml\Post;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends Post
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    public function __construct(
        JsonFactory $jsonFactory,
        PostRepositoryInterface $postRepository,
        Registry $registry,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;

        parent::__construct($postRepository, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();

        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error'    => true,
            ]);
        }

        foreach (array_keys($postItems) as $postId) {
            $post = $this->postRepository->get($postId);

            try {
                $data = $postItems[$postId];

                //@todo
                $post->addData($data);

                $this->postRepository->save($post);

            } catch (\Exception $e) {
                $messages[] = __('Something went wrong while saving the post.');
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error'    => $error,
        ]);
    }
}
