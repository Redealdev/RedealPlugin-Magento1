<?php
class Redeal_Referralmarketing_Model_Options
{
  /**
   * Provide available options as a value/label array
   *
   * @return array
   */
  public function getStoreConfig()
  {
    $write = Mage::getStoreConfig('redeal/redeal_group/section_two');
  }
  public function toOptionArray()
  {
    $write = array(
      array('value'=>1, 'label'=>'Live'),
      array('value'=>2, 'label'=>'Test')
      );
    return $write;
  }
}