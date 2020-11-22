<?php
namespace ant\shipping;

class ShippingCharges extends \ant\shipping\base\ShippingRule {
	public $cart;
	
	/*public function prepare() {
		$this->cart = $this->context['cart'];
	}*/
	public function apply() {
		if (isset($this->rules)) {
			foreach ($this->rules as $rule) {
				$rule = $this->instantiateRule($rule, $this);
				$rule->context = array_merge($this->context, $rule->context);
				
				if ($rule->match()) {
					return $rule->call();
				}
			}
		}
		return $this->default;
	}

	public static function basedOn($context) {
		return static::make($context);
	}

	public function calculate() {
		return $this->call();
	}

	public function default($default) {
		$this->default = $default;
		return $this;
	}
}
