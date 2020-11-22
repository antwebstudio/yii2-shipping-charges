<?php 
use ant\cart\models\Cart;
use tests\factories\CartFactory;

class ShippingChargesComponentCest
{
    public function _before(UnitTester $I)
    {
        // Yii::$app->user->login(\ant\user\models\User::find()->one());
    }

    // tests
    /**
     * [cart subtotal, shipping Option, city, expected charges]
     * 
     * non free shipping
     * @example [4, "Delivery", "Ampang", 5]
     * @example [8, "Delivery", "Puchong", 10]
     * 
     * free shipping
     * @example [5, "Delivery", "Ampang", 0]
     * @example [10, "Delivery", "Puchong", 0]
     * 
     * self pickup
     * @example [4, "Self Pickup", "Ampang", 0]
     * @example [4, "Self Pickup", "Puchong", 0]
     * @example [15, "Self Pickup", "Ampang", 0]
     * @example [15, "Self Pickup", "Puchong", 0]
     */
    public function testNoRule(UnitTester $I, \Codeception\Example $example)
    {
        $cart = CartFactory::create();
        $cart->cartItems[0]->setAttribute('unit_price', $example[0]);
        
        $shipTo = $this->createShipTo();
        $data = $shipTo->addressData;
        $data['city'] = $example[2];
        $shipTo->addressData = $data;
        
        $shippingPreference = new \ant\shipping\models\ShippingPreference;
        $shippingPreference->option = ['shippingOption' => $example[1]];

        $shippingFee = \ant\shipping\components\ShippingCharges::basedOn([
            'cart' => $cart,
            'shipTo' => $shipTo,
            'shippingPreference' => $shippingPreference, // Currently use billTo as shipTo
        ])->setRules([
            [
                'class' => \ant\shipping\calculators\CustomTenantShipping::class,
                'tenant' => 28,
            ]
        ])->default(8)->calculate();

        $I->assertEquals($example[3], $shippingFee);
    }

    protected function createShipTo() {
        
        $shipTo = $shipTo  = new \ant\contact\models\Contact;
        $shipTo->attributes = [
            'addressData' => [
                'city' => 'new city',
            ],
            'firstname' => '--',
            'lastname' => 'John',
            'contact_name' => null,
            'organization' => null,
            'contact_number' => '+60161234567',
            'email' => 'blank@email.com',
            'address_id' => $this->createAddress()->id,
            'status' => 0,
            'created_by' => null,
            'updated_by' => null,
            'created_at' => '2020-11-21 20:34:00',
            'updated_at' => '2020-11-21 20:34:00',
            'address_string' => null,
            'ic_passport' => null,
            'data' => null,
            'fax_number' => null,
        ];
        if (!$shipTo->save()) throw new \Exception(print_r($shipTo->errors, 1));

        return $shipTo;
    }

    protected function createAddress() {
        $address = new \ant\address\models\Address;
        $address->attributes = [
            'address_1' => 'street address',
            'country_id' => 1,
            'zone_id' => 1,
            'postcode' => '11060',
            'city' => 'new city',
        ];
        if (!$address->save()) throw new \Exception(print_r($address->errors, 1));

        return $address;
    }
}
