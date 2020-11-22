<?php
namespace ant\shipping\rules;

class ProductIdRule extends \ant\shipping\base\ShippingRule {
	public function match($rulesContext = []) {
		$this->prepare($rulesContext);
		
		if (isset($this->product)) {
			return in_array($this->product->id, (array) $this->context);
		} else {
			throw new \Exception('Product is not set. ');
			return !isset($this->context);
		}
	}
}