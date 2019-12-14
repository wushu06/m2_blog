<?php

namespace Tbb\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Tbb\Blog\Api\Repository\PostRepositoryInterface;
use Tbb\Blog\Controller\Adminhtml\Post;
use Tbb\Blog\Model\Config\FileProcessor;

class FileUpload extends Post
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    public function __construct(
        FileProcessor $fileProcessor,
        PostRepositoryInterface $postRepository,
        Registry $registry,
        Context $context
    ) {
        $this->fileProcessor = $fileProcessor;

        parent::__construct($postRepository, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $result = $this->fileProcessor->save(key($_FILES));
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
