<?php

namespace Tbb\Blog\Controller\Post;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Tbb\Blog\Controller\Post;

class View extends Post
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute()
    {
        $post = $this->initModel();

        if (!$post) {
            throw new NotFoundException(__('Page not found'));
            die;
        }

        /* @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->_eventManager->dispatch(
            'blog_page_render',
            ['post' => $post, 'controller_action' => $this]
        );

        return $resultPage;
    }
}
