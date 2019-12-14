<?php

namespace Tbb\Blog\Controller\Post;

use Magento\Framework\Controller\ResultFactory;

class Listing extends \Magento\Framework\App\Action\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected $_pageFactory;


    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        return $resultPage;

        $this->_view->loadLayout();
        $this->_view->renderLayout();

    }
   /*
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        return $resultPage;
    }*/
}
