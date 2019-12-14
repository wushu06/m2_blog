<?php

namespace Tbb\Blog\Controller\Adminhtml\Post;

use Tbb\Blog\Api\Data\PostInterface;
use Tbb\Blog\Controller\Adminhtml\Post;

class Delete extends Post
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {
            try {
                $this->postRepository->delete($model);

                $this->messageManager->addSuccessMessage(__('The post has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', [PostInterface::ID => $model->getId()]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('This post no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }
    }
}
