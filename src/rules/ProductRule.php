<?php
namespace ant\shipping\rules;

class ProductRule extends \ant\shipping\base\ShippingRule {
	public $product;
	
	public function match($rulesContext = []) {
		$this->prepare($rulesContext);
		
		if (isset($this->product)) {
			return in_array($this->product->id, (array) $this->rules['id']);
		} else {
			throw new \Exception('Product is not set. ');
			return !isset($this->rules['id']);
		}
	}
}