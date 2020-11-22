<?php

namespace ant\shipping\models;

use Yii;
use ant\helpers\ArrayHelper as Arr;
use ant\models\ModelClass;

/**
 * This is the model class for table "shipping_rule".
 *
 * @property int $id
 * @property int|null $rule_class_id
 * @property string|null $option
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ModelClass $ruleClass
 */
class ShippingRule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shipping_rule}}';
    }
	
	public function behaviors() {
		return [
			\ant\behaviors\AttachBehaviorBehavior::class,
			'configurable' => ['class' => \ant\behaviors\ConfigurableModel::class],
		];
	}
	
	public static function find() {
		return new \ant\shipping\models\query\ShippingRuleQuery(get_called_class());
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return $this->getCombinedRules([
			['priority', 'default', 'value' => 0],
			[['rule_class_id'], 'required'],
            [['rule_class_id', 'status', 'created_by', 'updated_by', 'priority'], 'integer'],
            [['option'], 'string'],
            [['created_at', 'updated_at', 'option'], 'safe'],
            [['rule_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => ModelClass::className(), 'targetAttribute' => ['rule_class_id' => 'id']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
		$labels = [];
		if (isset($this->ruleClass)) {
			$ruleClassName = $this->ruleClass->class_name;
			
			$attributes = (new \ReflectionClass($ruleClassName))->getProperties(\ReflectionProperty::IS_PUBLIC);
			foreach ($attributes as $item) {
				$labels['option['.$item->name.']'] = \yii\helpers\Inflector::camel2Words($item->name);
			};
		}
		
        return $this->getCombinedAttributeLabels(Arr::merge($labels, [
            'id' => 'ID',
            'rule_class_id' => 'Rule Class ID',
            'option' => 'Option',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ]));
    }

    /**
     * Gets query for [[RuleClass]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRuleClass()
    {
        return $this->hasOne(ModelClass::className(), ['id' => 'rule_class_id']);
    }
}
