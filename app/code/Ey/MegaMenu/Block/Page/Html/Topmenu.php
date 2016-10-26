<?php

namespace Ey\MegaMenu\Block\Page\Html;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Data\Tree\NodeFactory;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    /**
     * @var int
     */
    protected $rowCount = 10;

    /**
     * @var array
     */
    protected $treeLevelCount = array();

    /**
     * @var \Ey\MegaMenu\Helper\MegaMenu
     */
    protected $megaMenu;

    /**
     * Topmenu constructor.
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param \Ey\MegaMenu\Helper\MegaMenu $megaMenu
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        \Ey\MegaMenu\Helper\MegaMenu $megaMenu,
        array $data = []
    ){
        $this->megaMenu = $megaMenu;
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
    }

    /**
     * @return \Magento\Framework\Data\Tree\Node
     */
    public function getMenu()
    {
        return $this->_menu;
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            if (count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child);
            if($child->getMegamenuActivate()){
                $html .= ' data-mega-menu="true" ';
                $html .= ' data-id="' . $child->getId() . '" ';
            }
            $html .= '>';

            $name = $child->getShortName() ? $child->getShortName():$child->getName();

            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>' . $this->escapeHtml(
                    $name
                ) . '</span></a>';
            $html .= $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                );
            if($child->getMegamenuActivate()){
                $this->megaMenu->setNode($child);
                $html .= '<div class="mega-menu-container">';
                $html .= '<div class="mega-menu-div" data-id="'.$child->getId().'">';
                $html .= $this->megaMenu->getMegaMenu();
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</li>';
            $itemPosition++;
            $counter++;
        }

        if (count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }
}