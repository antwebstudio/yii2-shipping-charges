<?php
namespace ant\shipping\calculators;

class SumCartItemsQuantity extends \ant\shipping\base\ShippingRule {
	public $cart;
	public $rules = [];
	
	public function apply() {
		$total = 0;
		foreach ($this->cart->cartItems as $item) {
			if ($this->match($this->map($item))) {
				$total += $item->quantity;
			}
		}
		return $total;
	}
	
	public function map($context) {
		return ['product' => $context->item];
	}
}