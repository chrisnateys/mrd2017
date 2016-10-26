<?php

namespace Ey\MegaMenu\Model\Config\Source;

class Block extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
    }

    protected function _blockToArray()
    {
        $collection = $this->_objectManager->get('Magento\Cms\Model\ResourceModel\Block\Collection');
        $toOptArray = $collection->toOptionArray();
        array_unshift($toOptArray, array('value'=>'', 'label'=>"-----Select Static Block-----"));

        return $toOptArray;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->_blockToArray();
        }
        return $this->_options;
    }
}
