<?php

namespace Ey\MegaMenu\Controller\Adminhtml\Category;

/**
 * Class Save
 * @package Ey\MegaMenu\Controller\Adminhtml\Category
 */
class Save extends \Magento\Catalog\Controller\Adminhtml\Category\Save
{
    /**
     * Filter category data
     *
     * @param array $rawData
     * @return array
     */
    protected function _filterCategoryPostData(array $rawData)
    {
        $data = $rawData;
        // @todo It is a workaround to prevent saving this data in category model and it has to be refactored in future
        if (isset($data['image']) && is_array($data['image'])) {
            if (!empty($data['image']['delete'])) {
                $data['image'] = null;
            } else {
                if (isset($data['image'][0]['name']) && isset($data['image'][0]['tmp_name'])) {
                    $data['image'] = $data['image'][0]['name'];
                } else {
                    unset($data['image']);
                }
            }
        } if (isset($data['desktop_banner']) && is_array($data['desktop_banner'])) {
            if (!empty($data['desktop_banner']['delete'])) {
                $data['desktop_banner'] = null;
            } else {
                if (isset($data['desktop_banner'][0]['name']) && isset($data['desktop_banner'][0]['tmp_name'])) {
                    $data['desktop_banner'] = $data['desktop_banner'][0]['name'];
                } else {
                    unset($data['desktop_banner']);
                }
            }
        } if (isset($data['tablet_banner']) && is_array($data['tablet_banner'])) {
            if (!empty($data['tablet_banner']['delete'])) {
                $data['tablet_banner'] = null;
            } else {
                if (isset($data['tablet_banner'][0]['name']) && isset($data['tablet_banner'][0]['tmp_name'])) {
                    $data['tablet_banner'] = $data['tablet_banner'][0]['name'];
                } else {
                    unset($data['tablet_banner']);
                }
            }
        } if (isset($data['mobile_banner']) && is_array($data['mobile_banner'])) {
            if (!empty($data['mobile_banner']['delete'])) {
                $data['mobile_banner'] = null;
            } else {
                if (isset($data['mobile_banner'][0]['name']) && isset($data['mobile_banner'][0]['tmp_name'])) {
                    $data['mobile_banner'] = $data['mobile_banner'][0]['name'];
                } else {
                    unset($data['mobile_banner']);
                }
            }
        } if (isset($data['megamenu_image']) && is_array($data['megamenu_image'])) {
            if (!empty($data['megamenu_image']['delete'])) {
                $data['megamenu_image'] = null;
            } else {
                if (isset($data['megamenu_image'][0]['name']) && isset($data['megamenu_image'][0]['tmp_name'])) {
                    $data['megamenu_image'] = $data['megamenu_image'][0]['name'];
                } else {
                    unset($data['megamenu_image']);
                }
            }
        } if(isset($data['megamenu'])){
            $data['megamenu_html'] = $data['megamenu'];
            unset($data['megamenu']);
        }
        return $data;
    }

    /**
     * Image data preprocessing
     *
     * @param array $data
     *
     * @return array
     */
    public function imagePreprocessing($data)
    {
        if (empty($data['image'])) {
            unset($data['image']);
            $data['image']['delete'] = true;
        } if (empty($data['desktop_banner'])) {
            unset($data['desktop_banner']);
            $data['desktop_banner']['delete'] = true;
        } if (empty($data['tablet_banner'])) {
            unset($data['tablet_banner']);
            $data['tablet_banner']['delete'] = true;
        } if (empty($data['mobile_banner'])) {
            unset($data['mobile_banner']);
            $data['mobile_banner']['delete'] = true;
        } if (empty($data['megamenu_image'])) {
            unset($data['megamenu_image']);
            $data['megamenu_image']['delete'] = true;
        }
        return $data;
    }
}