<?php

namespace Ey\MegaMenu\Helper;

class MegaMenu extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var string
     */
    static $eyRegex = '/\[EY\](.*?)\[\/EY\]/s';

    /**
     * @var int
     */
    protected $rowCount = 10;

    /**
     * @var \Magento\Framework\Data\Tree\Node
     */
    protected $_node;

    /**
     * @var \Magento\Framework\Data\Tree\Node\Collection
     */
    protected $_children;

    /**
     * @var \Magento\Cms\Block\Block
     */
    protected $_block;

    /**
     * MegaMenu constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Cms\Block\Block $block
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Cms\Block\Block $block
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_block = $block;
        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\Data\Tree\Node $node
     */
    public function setNode($node)
    {
        $this->_node = $node;
    }

    /**
     * @return string
     */
    public function getMegaMenu()
    {
        if(!$this->_node || !$this->_node->getMegamenuHtml() || $this->_node->getMegamenuHtml() == ''){
            return '';
        }

        $html = $this->_node->getMegamenuHtml();
        $matches = array();
        $success = preg_match_all(self::$eyRegex, $html, $matches);
        if ($success) {
            foreach($matches[0] as $key => $match){
                $regex = '|'.preg_quote($match).'|';
                if(strpos($matches[1][$key], 'category-node') !== false){
                    $valueVal = $this->_getMenuHtml($matches[1][$key]);
                    $html = preg_replace($regex, $valueVal, $html);
                } elseif(strpos($matches[1][$key], 'static_block') !== false){
                    $valueVal = $this->_block->setBlockId(
                        $this->_node->getData($matches[1][$key])
                    )->toHtml();
                    $html = preg_replace($regex, $valueVal, $html);
                } elseif($this->_node->hasData($matches[1][$key])){
                    $valueVal = $this->_node->getData($matches[1][$key]);
                    $html = preg_replace($regex, $valueVal, $html);
                } else{
                    $html = preg_replace($regex, '', $html);
                }
            }

            return $html;
        }

        return $html;
    }

    /**
     * @param null|string $id
     * @return string
     */
    protected function _getMenuHtml($id)
    {
        $html = '';
        $child = $this->_getChildren($id);
        if(count($child) > 0){
            $html .= '<div class="mega-menu-1">';
            $html .= '<a href="'.$child['url'].'">'.$child['name'].'</a>';
            if(isset($child['children'])){
                $html .= '<div class="mega-menu-2">';

                $collectionSize = count($child['children']);
                $rowCount = $this->getRowCount();
                $i = 0;
                $colCount = ceil($collectionSize/$rowCount);
                foreach ($child['children'] as $childChild){
                    if ($i++%$rowCount==0){
                        $html .= '<div class="mega-menu-2-column" data-col="'.$colCount.'">';
                    }
                    $html .= '<div class="mega-menu-2-div">';
                    $html .= '<a href="'.$childChild['url'].'">'.$childChild['name'].'</a>';
                    $html .= '</div>';
                    if ($i%$rowCount==0 || $i==$collectionSize){
                        $html .= '</div>';
                    }
                }
                $html .= '</div>';
            }
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param null|string $id
     * @return array|\Magento\Framework\Data\Tree\Node\Collection
     */
    protected function _getChildren($id = null)
    {
        if($id && !isset($this->_children[$id])){
            $this->_children[$this->_node->getId()] = $this->_node->getData();
            foreach ($this->_node->getChildren() as $child){
                $this->_children[$child['id']] = $child->getData();
                $this->_children[$this->_node->getId()]['children'][$child['id']] = $child->getData();
                if($child->hasChildren()){
                    foreach ($child->getChildren() as $childChild){
                        $this->_children[$childChild['id']] = $childChild->getData();
                        $this->_children[$child['id']]['children'][$childChild['id']] = $childChild->getData();
                        if($childChild->hasChildren()){
                            foreach ($childChild->getChildren() as $childChildChild){
                                $this->_children[$childChild['id']]['children'][$childChildChild['id']] =
                                    $childChildChild->getData();
                            }
                        }
                    }
                }
            }
        }

        if($id){
            return isset($this->_children[$id])?$this->_children[$id]:array();
        }

        return $this->_children;
    }

    /**
     * @return int
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }
}