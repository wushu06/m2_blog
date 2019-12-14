<?php

namespace Tbb\Blog\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Tbb\Blog\Api\Data\PostInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Url
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var PostFactory
     */
    protected $postFactory;


    /**
     * @var MagentoUrlInterface
     */
    protected $urlManager;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        StoreManagerInterface $storeManager,
        Config $config,
        ScopeConfigInterface $scopeConfig,
        PostFactory $postFactory,
        MagentoUrlInterface $urlManager
    ) {
        $this->storeManager    = $storeManager;
        $this->config          = $config;
        $this->scopeConfig     = $scopeConfig;
        $this->postFactory     = $postFactory;
        $this->urlManager      = $urlManager;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlManager->getUrl($this->config->getBaseRoute());
    }

    /**
     * @param Post $post
     * @param bool $useSid
     * @return string
     */
    public function getPostUrl($post, $useSid = true)
    {
        return $this->getUrl('/' . $post->getUrlKey(), 'post', ['_nosid' => !$useSid]);
    }


    /**
     * @param array $urlParams
     * @return string
     */
    public function getSearchUrl($urlParams = [])
    {
        return $this->getUrl('/search/', 'search', $urlParams);
    }

    /**
     * @param string $route
     * @param string $type
     * @param array  $urlParams
     * @return string
     */
    protected function getUrl($route, $type, $urlParams = [])
    {
        echo '<pre>';
        var_dump($route);
        echo '</pre>';
        die();
        $url = $this->urlManager->getUrl($this->config->getBaseRoute() . $route, $urlParams);

        if ($type == 'post' && $this->config->getPostUrlSuffix()) {
            $url = $this->addSuffix($url, $this->config->getPostUrlSuffix());
        }


        return $url;
    }

    /**
     * @param string $url
     * @param string $suffix
     * @return string
     */
    private function addSuffix($url, $suffix)
    {
        $parts    = explode('?', $url, 2);
        $parts[0] = rtrim($parts[0], '/') . $suffix;

        return implode('?', $parts);
    }

    /**
     * @param string $pathInfo
     * @return bool|DataObject
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function match($pathInfo)
    {
        $identifier = trim($pathInfo, '/');
        $parts      = explode('/', $identifier);

        if (count($parts) >= 1) {
            $parts[count($parts) - 1] = $this->trimSuffix($parts[count($parts) - 1]);
        }

        if ($parts[0] != $this->config->getBaseRoute()) {
            return false;
        }

        if (count($parts) > 1) {
            unset($parts[0]);
            $parts  = array_values($parts);
            $urlKey = implode('/', $parts);
            $urlKey = urldecode($urlKey);
            $urlKey = $this->trimSuffix($urlKey);
        } else {
            $urlKey = '';
        }



        if ($parts[0] == 'rss' && isset($parts[1])) {
            $category = $this->categoryFactory->create()->getCollection()
                ->addFieldToFilter('url_key', $parts[1])
                ->getFirstItem();

            if ($category->getId()) {
                return new DataObject([
                    'module_name'     => 'blog',
                    'controller_name' => 'category',
                    'action_name'     => 'rss',
                    'params'          => [CategoryInterface::ID => $category->getId()],
                ]);
            } else {
                return false;
            }
        } elseif ($parts[0] == 'rss') {
            return new DataObject([
                'module_name'     => 'blog',
                'controller_name' => 'category',
                'action_name'     => 'rss',
                'params'          => [],
            ]);
        }

        $post = $this->postFactory->create()->getCollection()
            ->addPostFilter()
            ->addAttributeToFilter('url_key', $urlKey)
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->getFirstItem();

        if ($post->getId()) {
            return new DataObject([
                'module_name'     => 'blog',
                'controller_name' => 'post',
                'action_name'     => 'view',
                'params'          => [PostInterface::ID => $post->getId()],
            ]);
        }


        return false;
    }

    /**
     * Return url without suffix
     * @param string $key
     * @return string
     */
    protected function trimSuffix($key)
    {
        $suffix = $this->config->getCategoryUrlSuffix();
        //user can enter .html or html suffix
        if ($suffix != '' && $suffix[0] != '.') {
            $suffix = '.' . $suffix;
        }

        $key = str_replace($suffix, '', $key);

        $suffix = $this->config->getPostUrlSuffix();
        //user can enter .html or html suffix
        if ($suffix != '' && $suffix[0] != '.') {
            $suffix = '.' . $suffix;
        }

        $key = str_replace($suffix, '', $key);

        return $key;
    }
}