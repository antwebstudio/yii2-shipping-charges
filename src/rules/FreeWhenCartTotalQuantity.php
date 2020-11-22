<?php
namespace ant\shipping\rules;

class FreeWhenCartTotalQuantity extends \ant\shipping\base\ShippingRule {
	public $cart;
	public $total;
	public $categories = [];
	
	public function apply() {
		if (!isset($this->total)) throw new \Exception('Total property for '.get_class($this).' is not set. ');
		
		$quantity = 0;
		foreach($this->cart->cartItems as $item) {
			$product = $item->item;
			if (isset($this->categories) && count($this->categories)) {
				if (array_intersect($product->categories_ids, $this->categories)) {
					$quantity += $item->quantity;
				}
			} else {
				$quantity += $item->quantity;
			}
		}
		
		if ($quantity >= $this->total) {
			return 0;	
		}
	}
}