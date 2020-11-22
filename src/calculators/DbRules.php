<?php
namespace ant\shipping\calculators;

use Yii;
use ant\shipping\models\ShippingRule;

class DbRules extends \ant\shipping\base\ShippingRule {
	public $defaultPrice = false;
	public $returnNullValue = false;
	
	public function apply() {
		foreach ($this->getRules() as $rule) {
			$rule->setContext($this->context);
			
			if ($rule->match()) {
				$value = $rule->apply();
				if (isset($value) || $this->returnNullValue) {
					return $value;
				}
			}
		}
		return $this->defaultPrice;
	}
	
	protected function getRules() {
		$rulesAr = ShippingRule::find()->orderBy('priority desc')->all();
		$rules = [];
		foreach ($rulesAr as $rule) {
			$ruleConfig = json_decode($rule->option, true);
			$ruleConfig['class'] = $rule->ruleClass->class_name;
			$rules[] = Yii::createObject($ruleConfig);
		}
		return $rules;
	}
}