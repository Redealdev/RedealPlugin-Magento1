<?php
/**
* Magento
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magento.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade Magento to newer
* versions in the future. If you wish to customize Magento for your
* needs please refer to http://www.magento.com for more information.
*
* @category    Mage
* @package     Mage_Checkout
* @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/


class Redeal_Referralmarketing_Block_Onepage_Success extends Mage_Checkout_Block_Onepage_Success
{
    private $_order;

    public function getOrder()
    {
        $lastOrderId = $this->_getData('order_id');;
        if (empty($lastOrderId)) {
            return null;
        }

        $order = Mage::getModel('sales/order')->loadByIncrementId($lastOrderId);
        return $order;
    }

    public function getItems()
    {
        $order = $this->getOrder();
        $store = $this->getStore();
        if (empty($order)) {
            return array();
        }

        $data = array();

        foreach($order->getAllVisibleItems() as $item) { 

            if( $item->getParentItemId() ) {
                continue;
            }

            $product = $item->getProduct();

            $taxCalculation = Mage::getModel('tax/calculation');
            $request = $taxCalculation->getRateRequest(null, null, null, $store);
            $taxClassId = $product->getTaxClassId();
            $taxpercent = $taxCalculation->getRate($request->setProductClassId($taxClassId));

            $price = $product->getPrice();
            $specialPrice = $product->getSpecialprice();
            if (($specialPrice > 0) && ($specialPrice < $price)) {
                $price = $specialPrice;
            }
            $tax = ($price / (100 + $taxpercent)) * $taxpercent;

            $data[] = array(
                'sku' => $this->quoteEscape($item->getSku()),
                'price' => number_format($price, 2, '.', ''),
                'category' => $this->getProductCategories($product),
                'quantity' => number_format($item->getQtyOrdered()),
                );
        }

        $prodata = json_encode($data);

        $prodetail = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',$prodata);

        return $prodetail;
    }

    public function getProductCategories(Mage_Catalog_Model_Product $product, $categorySeparator = ' > ', $pathSeparator = ' | ')
    {
        $allCategories = $this->loadAllCategories();
        $categoryPaths = [];

        foreach ($product->getCategoryIds() as $categoryId) {
            $category = $allCategories[$categoryId];
            $categoryPath = [];
            foreach ($category['path'] as $pathId) {
                if ($pathId ==  1) {
                    continue;
                }

                if ($pathId ===  Mage::app()->getStore()->getRootCategoryId()) {
                    continue;
                }

                $categoryPath[] = $allCategories[$pathId]['name'];
            }

            $categoryPaths[] = implode($categorySeparator, $categoryPath);
        }

        return implode($pathSeparator, $categoryPaths);
    }

/**
* @return array
*/
protected function loadAllCategories()
{
    static $listing = [];
    if (!empty($listing)) {
        return $listing;
    }

    $collection = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name');
    foreach ($collection as $category) {

        $listing[$category->getId()] = ['name' => $category->getName(), 'path' => $category->getPathIds()];
    }

    return $listing;
}
public function getCustomerOrderDetail()
{
    $orderid = $this->_getData('order_id');
    $customerAddress = array();

    $orderdetil = Mage::getModel('sales/order')->loadByIncrementId($orderid); 

    $customer = Mage::getModel('customer/customer')->load($orderdetil->getCustomerId());
    foreach ($customer->getAddresses() as $address)
    {
        $customerAddress[] = $address->toArray();

    }
    return $customerAddress;
}
public function _getHeadContainerSnippet()
{
// Get the container ID.
    $enabled = Mage::helper('referralmarketing')->isEnabled();
    if($enabled == 1){
        $containerId = Mage::helper('referralmarketing')->getContainerId();

        return "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','".$containerId."');</script>\n";
}else{
    return false;
}

}
public function _getContainerSnippet()
{
    $enabled = Mage::helper('referralmarketing')->isEnabled();
    if($enabled == 1){
        $containerId = Mage::helper('referralmarketing')->getContainerId();

        return "<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=".$containerId."\"
        height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>\n";
}else{
    return false;
}

}

}
