<?php
namespace ant\shipping\calculators;

class ToBeConfirmed extends \ant\shipping\base\ShippingRule {
	public function apply() {
		return false;
	}
}