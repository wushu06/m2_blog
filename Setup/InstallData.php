<?php

namespace Tbb\Blog\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Tbb\Blog\Setup\InstallData\PostSetupFactory;
use Magento\Eav\Model\Config;

class InstallData implements InstallDataInterface
{
    /**
     * @var PostSetupFactory
     */
    protected $postSetupFactory;


    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @param PostSetupFactory     $postSetupFactory
     * @param Config               $eavConfig
     */
    public function __construct(
        PostSetupFactory $postSetupFactory,
        Config $eavConfig
    ) {
        $this->postSetupFactory = $postSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $postSetup = $this->postSetupFactory->create(['setup' => $setup]);
        $postSetup->installEntities();
        $setup->endSetup();
        $this->eavConfig->clear();

    }
}