<?php

namespace Tbb\Blog\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Tbb\Blog\Api\Data\PostInterface;
use Tbb\Blog\Api\Repository\PostRepositoryInterface;
use Tbb\Blog\Model\PostFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Store\Model\StoreManagerInterfMirasvitace;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Post extends Action
{
    /**
     * @var PostFactory
     */
    protected $postRepository;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var StoreManagerInterface
     */
//    protected $storeManager;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var JsonFactory
     */
//    protected $jsonFactory;

    /**
     * @var JsHelper
     */
//    protected $jsHelper;

    public function __construct(
        PostRepositoryInterface $postRepository,
//        StoreManagerInterface $storeManager,
//        JsonFactory $jsonFactory,
//        JsHelper $jsHelper,
        Registry $registry,
//        TimezoneInterface $localeDate,
        Context $context
    ) {
        $this->postRepository = $postRepository;
//        $this->storeManager = $storeManager;
//        $this->jsonFactory = $jsonFactory;
//        $this->jsHelper = $jsHelper;
        $this->registry = $registry;
//        $this->localeDate = $localeDate;
        $this->context = $context;

        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @param \Magento\Backend\Model\View\Result\Page\Interceptor $resultPage
     * @return \Magento\Backend\Model\View\Result\Page\Interceptor
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Tbb_Blog::blog');
        $resultPage->getConfig()->getTitle()->prepend(__('Tbb Blog MX'));
        $resultPage->getConfig()->getTitle()->prepend(__('Posts'));

        return $resultPage;
    }

    /**
     * @return PostInterface
     */
    public function initModel()
    {
        $model = $this->postRepository->create();
        $id = $this->getRequest()->getParam(PostInterface::ID);

        if ($id && !is_array($id)) {
            $model = $this->postRepository->get($id);
        }

        $this->registry->register('current_model', $model);

        return $model;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
        return $this->context->getAuthorization()->isAllowed('Tbb_Blog::blog_post');
    }
}
