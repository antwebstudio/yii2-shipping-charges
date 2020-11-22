<?php
namespace ant\shipping\rules;

class FreeWhenCartSubtotal extends \ant\shipping\base\ShippingRule {
	public $cart;
	public $total;
	public $default;
	
	public function apply() {
		if (!isset($this->total)) throw new \Exception('Total property for '.get_class($this).' is not set. ');
		
		if ($this->cart->subtotal >= $this->total) {
			return 0;	
		}
		return $this->default;
	}
}