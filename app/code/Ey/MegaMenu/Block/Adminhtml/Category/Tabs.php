<?php

namespace Ey\MegaMenu\Block\Adminhtml\Category;

class Tabs extends \Ey\LifeStyle\Block\Adminhtml\Category\Tabs
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $tabs = parent::_prepareLayout();
        if(is_array($tabs->_tabs)){
            foreach ($tabs->_tabs as $tabId => $tab){
                if($tab->getTitle()->getText() == 'Mega Menu'){
                    $_tabId = $tabId;
                    break;
                }
            }
        }
        if(isset($_tabId)) {
            $tabs->addTabAfter(
                $_tabId,
                [
                    'label' => __('Mega Menu'),
                    'content' => $this->getTabContent($tabs->_tabs[$_tabId]).
                        $this->getLayout()->createBlock(
                            'Ey\MegaMenu\Block\Adminhtml\Category\Tree',
                            'megamenu.tree'
                        )->toHtml()
                ],
                'merchandiser_content'
            );
        }

        return $tabs;
    }
}