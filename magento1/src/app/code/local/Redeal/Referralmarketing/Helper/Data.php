<?php
class Redeal_Referralmarketing_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_ACTIVE = 'redeal/redeal_group/redeal_select';
	const XML_PATH_CONTAINER = 'redeal/redeal_group/containerid';

	public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_ACTIVE);
            
    }
	public function getContainerId() {
		return Mage::getStoreConfig(self::XML_PATH_CONTAINER);
	}
}
