<?php 
namespace ant\shipping\behaviors;

use Yii;
use ant\models\ModelClass;
use ant\shipping\models\ShippingPreference;

class Shippable extends \ant\behaviors\MorphBehavior 
{
	protected $_preference;
	
	public function ensureShippingPreference() {
		if (isset($this->owner->shippingPreference)) {
			return $this->owner->shippingPreference;
		} else {
			$preference = new ShippingPreference([
				'shippable_id' => $this->owner->id,
				'shippable_class_id' => ModelClass::getClassId($this->owner),
			]);
			return $preference;
		}
	}
	
	public function getShippingPreference() {
		return $this->morphOne(ShippingPreference::class, 'shippable');
	}
}