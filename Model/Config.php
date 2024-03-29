<?php

namespace Tbb\Blog\Model;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const MEDIA_FOLDER = 'blog';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var MagentoUrlInterface
     */
    protected $urlManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param MagentoUrlInterface $urlManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        MagentoUrlInterface $urlManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->urlManager = $urlManager;
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getMenuTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'blog/appearance/menu_title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getBlogName($store = null)
    {
        return $this->scopeConfig->getValue(
            'blog/appearance/blog_name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return bool
     */
    public function isDisplayInMenu()
    {
        return $this->scopeConfig->getValue('blog/display/main_menu',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getExcerptsEnabled()
    {
        return $this->scopeConfig->getValue('blog/display/enable_excerpts',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    /**
     * @return string
     */
    public function getExcerptSize()
    {
        return $this->scopeConfig->getValue('blog/display/excerpt_size',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getBaseMetaTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'blog/seo/base_meta_title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getBaseMetaDescription($store = null)
    {
        return $this->scopeConfig->getValue(
            'blog/seo/base_meta_description',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getBaseMetaKeywords($store = null)
    {
        return $this->scopeConfig->getValue(
            'blog/seo/base_meta_keywords',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return string
     */
    public function getBaseRoute()
    {
        return $this->scopeConfig->getValue('blog/seo/base_route');
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlManager->getUrl($this->getBaseRoute());
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getPostUrlSuffix($store = null)
    {
        return $this->scopeConfig->getValue(
            'blog/seo/post_url_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getCategoryUrlSuffix($store = null)
    {
        return $this->scopeConfig->getValue(
            'blog/seo/category_url_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }


    /**
     * @param null|string $store
     * @return string
     */
    public function getDateFormat($store = null)
    {
        return $this->scopeConfig->getValue(
            'blog/appearance/date_format',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return string
     */
    public function getDefaultSortField()
    {
        return 'created_at';
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getCommentProvider($store = null)
    {
        return $this->scopeConfig->getValue(
            'blog/comments/provider',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return string
     */
    public function getDisqusShortname()
    {
        return $this->scopeConfig->getValue('blog/comments/disqus_shortname');
    }

    /**
     * @param null|string $store
     * @return bool
     */
    public function isAddThisEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            'blog/sharing/enable_addthis',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return string
     */
    public function getMediaPath($image)
    {
        $path = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath() . self::MEDIA_FOLDER;

        if (!file_exists($path) || !is_dir($path)) {
            $this->filesystem
                ->getDirectoryWrite(DirectoryList::MEDIA)
                ->create($path);
        }

        return $path . '/' . $image;
    }

    /**
     * @param string $image
     * @return string
     */
    public function getMediaUrl($image)
    {
        if (!$image) {
            return false;
        }

        $url = $this->storeManager->getStore()
                ->getBaseUrl(MagentoUrlInterface::URL_TYPE_MEDIA) . self::MEDIA_FOLDER;

        $url .= '/' . $image;

        return $url;
    }
}
