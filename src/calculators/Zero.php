<?php
namespace ant\shipping\calculators;

class Zero extends \ant\shipping\base\ShippingRule {
	public function apply() {
		return 0;
	}
}