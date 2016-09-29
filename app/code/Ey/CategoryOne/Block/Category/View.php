<?php
namespace Ey\CategoryOne\Block\Category;




class View extends \Magento\Catalog\Block\Category\View
{


    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryOutputHelper;


    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Helper\Output $categoryOutputHelper,
        \Magento\Catalog\Model\Category $categoryModel,
        array $data = []
    ){
        $this->categoryModel = $categoryModel;
        $this->categoryOutputHelper = $categoryOutputHelper;
        parent::__construct($context, $layerResolver, $registry,  $categoryHelper,  $data);
    }


    /**
     * @return $collection
     */
    public function getCategoryList()
    {
        $_category  = $this->getCurrentCategory();
        $collection = $this->getChildrenCategories($_category);
        return $collection;

    }

    /**
     *
     * @param  $cat
     * @return string $imgHtml
     *
     */
    public function getSubCategoryImage($cat){

        if ($_imgUrl = $cat->getImageUrl()) {
            $imgHtml = '<img src="' . $_imgUrl . '" />';
        }else{
            $_imgUrl = $this->getBaseUrl().'/pub/static/frontend/Ey/ey-mrd2016/en_US/Magento_Catalog/images/product/placeholder/image.jpg';
            $imgHtml = '<img src="' . $_imgUrl . '" />';
        }

        return $imgHtml;
    }


    /**
     * Return child categories
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getChildrenCategories($category)
    {
        $collection = $category->getCollection();
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $collection->addAttributeToSelect('*')->addAttributeToFilter(
            'is_active',
            1
        )->addIdFilter(
            $category->getChildren()
        )->setOrder(
            'position',
            \Magento\Framework\DB\Select::SQL_ASC
        )->joinUrlRewrite()->load();

        return $collection;
    }


}



?>