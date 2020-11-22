<?php

namespace ant\shipping\models;

use Yii;
use ant\models\ModelClass;

class ShippingOption extends \yii\base\Model {
	public $id;
	
	public $value = [];
	
	public static function findOne($id) {
		$tenant = \ant\tenant\models\Tenant::getCurrent();
		
		$shippingOptions = $tenant->shippingConfig->getShippingOptions();
		
		if (!isset($shippingOptions[$id])) throw new \Exception('Unknown shipping option value: '.$id);
		
		return new self(['id' => $id, 'value' => $shippingOptions[$id]]);
	}
	
	public function getCharges() {
		return isset($this->value['charges']) ? $this->value['charges'] : null;
	}
	
	public function getIsSelfPickup() {
		return isset($this->value['selfPickup']) ? $this->value['selfPickup'] : null;
	}
	
	public function getLabel() {
		return $this->id;
	}
}