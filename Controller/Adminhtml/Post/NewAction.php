<?php

namespace Tbb\Blog\Controller\Adminhtml\Post;

use Tbb\Blog\Controller\Adminhtml\Post;

class NewAction extends Post
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultRedirectFactory->create()->setPath('*/*/edit');
    }
}
