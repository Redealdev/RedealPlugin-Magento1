<?php
namespace Redeal\Referralmarketing\Model;
class Live extends \Magento\Framework\App\Config\Value implements \Magento\Framework\Option\ArrayInterface
{
    const Live = 2;

    const Test = 3;

	public function toOptionArray()
    {
        return [self::Live => __('Live'), self::Test => __('Test')];
    }
}
