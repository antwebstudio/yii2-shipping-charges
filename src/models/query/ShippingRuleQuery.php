<?php 
namespace ant\shipping\models\query;

class ShippingRuleQuery extends \yii\db\ActiveQuery {
	public function behaviors() {
		return [
			\ant\behaviors\AttachBehaviorBehavior::class,
		];
	}
}