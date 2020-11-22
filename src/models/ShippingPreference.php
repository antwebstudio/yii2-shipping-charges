<?php

namespace ant\shipping\models;

use Yii;
use ant\models\ModelClass;

/**
 * This is the model class for table "{{%shipping_preference}}".
 *
 * @property int $id
 * @property int|null $shippable_id
 * @property int|null $shippable_class_id
 * @property int|null $courier_id
 * @property string|null $option
 * @property string|null $delivery_date
 * @property string|null $delivery_remark
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property ModelClass $shippableClass
 */
class ShippingPreference extends \yii\db\ActiveRecord
{
	use \ant\traits\ActiveRecordTrait;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shipping_preference}}';
    }
	
	public function behaviors() {
		return [
			[
				'class' => \ant\behaviors\MorphBehavior::class,
			],
			'configurable' => [
				'class' => \ant\behaviors\ConfigurableModel::class,
			],
			[
				'class' => \ant\behaviors\SerializableAttribute::class,
				'attributes' => ['option'],
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return $this->getCombinedRules([
			//[['shippable_id', 'shippable_class_id'], 'required'],
            [['shippable_id', 'shippable_class_id', 'courier_id', 'created_by', 'updated_by'], 'integer'],
            [['option', 'delivery_date', 'created_at', 'updated_at'], 'safe'],
            [['delivery_remark'], 'string', 'max' => 255],
            [['shippable_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => ModelClass::className(), 'targetAttribute' => ['shippable_class_id' => 'id']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->getCombinedAttributeLabels([
            'id' => 'ID',
            'shippable_id' => 'Shippable ID',
            'shippable_class_id' => 'Shippable Class ID',
            'courier_id' => 'Courier ID',
            'option' => 'Option',
            'delivery_date' => 'Delivery Date',
            'delivery_remark' => 'Delivery Remark',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ]);
    }
	
	public function getAttributeLabel($attribute) {
		return Yii::t('shipping', parent::getAttributeLabel($attribute));
	}

    /**
     * Gets query for [[ShippableClass]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShippableClass()
    {
        return $this->hasOne(ModelClass::className(), ['id' => 'shippable_class_id']);
    }
	
	public function getShippable() {
		return $this->morphBelongsTo('shippable');
	}
	
	public function getShippingOption() {
		if (isset($this->option['value']) && !isset($this->option['shippingOption'])) {
			$option = $this->option;
			$option['shippingOption'] = $option['value'];
			$this->option = $option;
		}
		if (isset($this->option['shippingOption'])) {
			$shippingOption = \ant\shipping\models\ShippingOption::findOne($this->option['shippingOption']);
			if (!isset($shippingOption)) throw new \Exception('Unknown shipping option ID: '.$this->option['shippingOption']);
			return $shippingOption;
		} 
	}
	
	public function getIsAddressRequired() {
		return !$this->isSelfPickup;
	}
	
	public function getIsSelfPickup() {
		if (isset($this->option['selfPickup'])) {
			return $this->option['selfPickup'];
		}
		return $this->shippingOption->isSelfPickup;
	}
	
	public function getCharges() {
		return isset($this->shippingOption->charges) ? $this->shippingOption : null;
	}
}
