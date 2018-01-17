<?php

namespace Redeal\Referralmarketing\Block\Onepage;

class Success extends \Magento\Checkout\Block\Onepage\Success {

    public function getOrder() 
    {
		$objectManager =\Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManager->get('Redeal\Referralmarketing\Helper\Data');
    	 
		$lastOrderId = $this->getOrderId();
		 
		if (empty($lastOrderId)) 
		{
            return null;
        }
		  $orderData = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($this->getOrderId());
         
    	
        return $orderData;
    }
    public function getItems()
    {
    	$order = $this->getOrder();

    	$products = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productDetail = [];
            $productDetail['name'] = html_entity_decode($item->getName());
          
            $productDetail['price'] = number_format($item->getBasePrice(), 2, '.', '');
          
            $productDetail['quantity'] = $item->getQtyOrdered();
            $price = $item->getBasePrice();
             $data[] = array(
                'sku' => html_entity_decode($item->getSku()),
                'price' => number_format($price, 2, '.', ''),
                'category' => $this->getProductCategories($product),
                'quantity' => number_format($item->getQtyOrdered()),
                );
        }
        $prodata = json_encode($data);

        $prodetail = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',$prodata);

       
        return $prodetail;
    }

    public function getProductCategories($product, $categorySeparator = ' > ', $pathSeparator = ' | ')
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

                if ($pathId == 2) {
                    continue;
                }

                $categoryPath[] = $allCategories[$pathId]['name'];
            }

            $categoryPaths[] = implode($categorySeparator, $categoryPath);
        }

        return implode($pathSeparator, $categoryPaths);
    }
    protected function loadAllCategories()
	{
		static $listing = [];
    if (!empty($listing)) {
        return $listing;
    }
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$categoryobj = $objectManager->create('Magento\Catalog\Model\CategoryFactory')->create();
		$collection = $categoryobj->getCollection()->addAttributeToSelect('name');
		
	    foreach ($collection as $category) {

	        $listing[$category->getId()] = ['name' => $category->getName(), 'path' => $category->getPathIds()];
	    }
	    return $listing;
	}

	public function getCustomerOrderDetail()
	{
	    $lastOrderId = $this->getOrderId();
		$order = $this->getOrder(); 
	    $customerAddress = array();
	    foreach ($order->getShippingAddress() as $address)
	    {
	        $customerAddress[] = $address;

	    }
	    return $customerAddress;
	}
    public function _getHeadContainerSnippet()
    {
    // Get the container ID.
        $objectManager =\Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('Redeal\Referralmarketing\Helper\Data');
        $enableModule = $helper->getConfigValue('redeal_general/general/active');
        if($enableModule == 1){
            $containerId = $helper->getConfigValue('redeal_general/general/containerid');

        //return $enableModule;
        }
        else{
            return false;
        }

    }
    public function _getContainerSnippet()
    {
        $objectManager =\Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('Redeal\Referralmarketing\Helper\Data');
        $enableModule = $helper->getConfigValue('redeal_general/general/active');

        if($enableModule == 1)
        {
            $containerId = $helper->getConfigValue('redeal_general/general/containerid');

             //return $enableModule;
        }
        else{
            return false;
        }

    }
     public function loadAllData()
    {
        $objectManager =\Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('Redeal\Referralmarketing\Helper\Data');
        //$write = $helper->getConfigValue('redeal_general/general/custommode');
        $write = $helper->getConfigValue('redeal_general/general/custommode');
        // echo $write;
        // exit();
        if($write == 2)
        {
            $redlurl = "//widget.redeal.se/js/redeal.js";
        }
        else
        {
            $redlurl = "//test-widget.redeal.se/js/redeal.js";
        }

    }

}