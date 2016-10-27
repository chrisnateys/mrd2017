<?php

namespace Ey\MegaMenu\Plugin\Block;

/**
 * Class Topmenu
 * @package Ey\MegaMenu\Plugin\Block
 */
use Magento\Framework\Data\Tree\Node;

class Topmenu extends \Magento\Catalog\Plugin\Block\Topmenu
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $_layerResolver;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     */
    public function __construct(
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver
    ) {
        $this->_collectionFactory = $categoryCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_layerResolver = $layerResolver;
        parent::__construct($catalogCategory, $categoryCollectionFactory, $storeManager, $layerResolver);
    }

    /**
     * Get current Category from catalog layer
     *
     * @return \Magento\Catalog\Model\Category
     */
    protected function getCurrentCategory()
    {
        $catalogLayer = $this->_layerResolver->get();

        if (!$catalogLayer) {
            return null;
        }

        return $catalogLayer->getCurrentCategory();
    }

    /**
     * Build category tree for menu block.
     *
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return void
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {
        $rootId = $this->_storeManager->getStore()->getRootCategoryId();
        $storeId = $this->_storeManager->getStore()->getId();
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->getCategoryTree($storeId, $rootId);
        $currentCategory = $this->getCurrentCategory();
        $mapping = [$rootId => $subject->getMenu()];  // use nodes stack to avoid recursion
        foreach ($collection as $category) {
            if (!isset($mapping[$category->getParentId()])) {
                continue;
            }
            /** @var Node $parentCategoryNode */
            $parentCategoryNode = $mapping[$category->getParentId()];

            $categoryNode = new Node(
                $this->getCategoryAsAnArray($category, $currentCategory),
                'id',
                $parentCategoryNode->getTree(),
                $parentCategoryNode
            );
            $parentCategoryNode->addChild($categoryNode);

            $mapping[$category->getId()] = $categoryNode; //add node in stack
        }
    }

    /**
     * Convert category to array
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Model\Category $currentCategory
     * @return array
     */
    public function getCategoryAsAnArray($category, $currentCategory)
    {
        $data = [
            'name' => $category->getName(),
            'id' => 'category-node-' . $category->getId(),
            'url' => $this->catalogCategory->getCategoryUrl($category),
            'has_active' => in_array((string)$category->getId(), explode('/', $currentCategory->getPath()), true),
            'is_active' => $category->getId() == $currentCategory->getId(),
            'short_name' => $category->getShortName()
        ];
        $data['megamenu_activate'] = $category->getMegamenuActivate() == '1' ? true:false;
        $data['megamenu_image'] = $category->getMegamenuImage();
        $data['megamenu_image_url'] = $category->getMegamenuImageUrl();
        $data['megamenu_html'] = $category->getMegamenuHtml();
        $data['megamenu_static_block'] = $category->getMegamenuStaticBlock();

        return $data;
    }

    /**
     * Get Category Tree
     *
     * @param int $storeId
     * @param int $rootId
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCategoryTree($storeId, $rootId)
    {
        $collection = parent::getCategoryTree($storeId, $rootId);
        $collection->addAttributeToSelect(
            array(
                'megamenu_activate', 'megamenu_image', 'megamenu_image_url', 'megamenu_html', 'megamenu_static_block'
            )
        );

        return $collection;
    }
}