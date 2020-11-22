<?php
namespace ant\shipping\rules;

class FlatRate extends \ant\shipping\base\ShippingRule {
	public $cart;
	public $price;
	
	public function apply() {
		if (!isset($this->price)) throw new \Exception('Price property for '.get_class($this).' is not set. ');
		
		return $this->price;
	}
}