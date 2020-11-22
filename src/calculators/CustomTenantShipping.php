<?php
namespace ant\shipping\calculators;

class CustomTenantShipping extends \ant\shipping\base\ShippingRule {
	public $tenant;
	public $cart;
	public $shippingPreference;
	public $shipTo;
	
	public function apply() {
		if ($this->tenant == 2) {
			$productIds = [1, 2, 3, 4, 5, 6, 7, 8];
			
			$quantity = 0;
			foreach($this->cart->cartItems as $item) {
				if (in_array($item->item_id, $productIds)) {
					$quantity += $item->quantity;
				}
			}
			return $quantity >= 10 ? 0 : 3;
		} else if ($this->tenant == 7) {
			$categoryIds = [3];
			
			$quantity = 0;
			foreach($this->cart->cartItems as $item) {
				$product = $item->item;
				if (array_intersect($product->categories_ids, $categoryIds)) {
					$quantity += $item->quantity;
				}
			}
			return $quantity >= 2 ? 0 : 5;
		} else if ($this->tenant == 8) {
			return 0;
		} else if ($this->tenant == 9) {
			// Sunwai
			if (isset($this->shippingPreference)) { // This information may not available when after user checkout, but have fill their address yet. 
				$selected = $this->shippingPreference->getShippingOption();
				
				return $selected->charges;
			}
			return 5; // Return default value when user have fill in their address
		} else if ($this->tenant == 11) {
			return 0;
		} else if ($this->tenant == 5) {
			if ($this->cart->subtotal >= 50) {
				return 0;
			}
			return 5;
		} else if ($this->tenant == 12) {
			$config = self::config($this->tenant);
			
			if (isset($this->shipTo)) { // This information may not available when after user checkout, but have fill their address yet. 
				$selected = isset($config['cities'][$this->shipTo->addressData['city']]) ? $config['cities'][$this->shipTo->addressData['city']] : null;
				if (isset($selected['charges'])) {
					return $selected['charges'] === false ? 0 : $selected['charges'];
				}
			}
			return 5;
		} else if ($this->tenant == 13) {
			// AiAi Healthy Noodles Sdn Bhd	

			if ($this->cart->subtotal >= 40) {
				return 0;
			}
			return 5;
		} else if ($this->tenant == 14) {
			// Naked farm
			
			if (isset($this->shippingPreference)) { // This information may not available when after user checkout, but have fill their address yet. 
				$selected = $this->shippingPreference->getShippingOption();
				
				return $selected->charges;
			}
			return 15;
		} else if ($this->tenant == 15) {
			return false;
		} else if ($this->tenant == 17) {
			// Vegeboys
			if ($this->cart->subtotal >= 100) {
				return 0;
			}
			
			$config = self::config($this->tenant, $this->context);
			
			
			if (isset($this->shippingPreference)) { // This information may not available when after user checkout, but have fill their address yet. 
				$selected = $this->shippingPreference->getShippingOption();
				
				if (is_array($selected->charges)) {
					return $this->processRules($selected->charges);
				}
				return $selected->charges;
			}
			
			return false;
			
		} else if ($this->tenant == 16) {
			return 10;
		} else if ($this->tenant == 28) {
			$config = self::config($this->tenant, $this->context);
			$shippingOptions = self::getShippingOptions($this->tenant);
			$selected = $this->shippingPreference->option['shippingOption'];

			$selectedCity = $this->shipTo->addressData['city'];
			$selectedShippingOption = $shippingOptions[$selected];

			if (is_array($selectedShippingOption['charges'])) {
				return $this->processRules($selectedShippingOption['charges']);
			}

			// Charge base on shipping option
			if (isset($selectedShippingOption['charges'])) {
				return $selectedShippingOption['charges'];
			}

			// Charge base on city selected
			return $config['cities'][$selectedCity]['charges'];
		}
		return 5;
	}

	protected function getSelectedCityGroup() {
		$config = self::config($this->tenant, $this->context);
		$selectedCity = $this->shipTo->addressData['city'];
		return $config['cities'][$selectedCity]['group'];
	}
	
	protected function processRules($rules) {
		foreach ($rules as $rule) {
			$method = 'rule'.ucfirst($rule['rule']);
			$charges = $this->{$method}($rule);
			if (isset($charges)) {
				return $charges;
			}
		}
	}
	
	protected function ruleCartTotal($rule) {
		if (!isset($rule['cityGroup']) || $rule['cityGroup'] == $this->getSelectedCityGroup()) {
			if ($this->cart->subtotal >= $rule['total']) {
				return $rule['charges'];
			}
		}
	}
	
	public static function getCitiesDropDown($tenantId) {
		return self::getDropDown(self::getCitiesConfig($tenantId));
	}
	
	public static function getShippingOptionsDropDown($tenantId) {
		return self::getDropDown(self::getShippingOptions($tenantId), null, null, true);
	}
	
	public static function getCitiesConfig($tenantId) {
		$config = self::config($tenantId);
		return isset($config['cities']) ? $config['cities'] : null;
	}
	
	public static function getShippingOptions($tenantId) {
		$config = self::config($tenantId);
		return isset($config['options']) ? $config['options'] : null;
	}
	
	protected static function getDropDown($options, $labelAttribute = null, $valueAttribute = null, $translate = false) {
		if (isset($options)) {
			$dropdown = [];
			foreach ($options as $name => $charges) {
				$dropdown[$name] = $translate ? \Yii::t('shipping', $name) : $name;
			}
			return $dropdown;
		}
	}
	
	public static function config($tenantId, $context = []) {
		if ($tenantId == 9) {
			return [
				'options' => [
					'Farlim / Air Itam' => ['charges' => 3],
					'Bayan Baru,  Sg. Ara' => ['charges' => 5],
					'Self pick up (Green Lane Shell Petrol Station)' => [
						'charges' => 0, 
						'hideAddress' => true, 
						'selfPickup' => true,
						'note' => 'Self pick up at Green Lane Shell Petrol Station (4A, Jalan Masjid Negeri) which has BHP Petrol Station in the same row.'
					],
				],
			];
		} else if ($tenantId == 28) {
			return [
				'options' => [
					'Self Pickup' => [
						'charges' => 0,
						'hideAddress' => true, 
						'selfPickup' => true,
					],
					'Delivery' => [
						'charges' => [
							[
								'rule' => 'cartTotal',
								'total' => 5,
								'charges' => 0,
								'cityGroup' => 5
							],
							[
								'rule' => 'cartTotal',
								'total' => 0,
								'charges' => 5,
								'cityGroup' => 5
							],
							[
								'rule' => 'cartTotal',
								'total' => 10,
								'charges' => 0,
								'cityGroup' => 10
							],
							[
								'rule' => 'cartTotal',
								'total' => 0,
								'charges' => 10,
								'cityGroup' => 10
							],
						],
					],
				],
				'cities' => [
					'Ampang' => [
						'group' => 5
					],
					'Kuala Lumpur' => [
						'group' => 10,
					],
					'Petaling Jaya' => [
						'group' => 10,
					],
					'Puchong' => [
						'group' => 10,
					],
					'Subang' => [
						'group' => 10,
					],
				],
			];
		} else if ($tenantId == 7) {
			return [
				'cities' => [
					'Air Itam' => [
						//'availableDate' => ['23/4', '26/4'],
					],
					'Bayan Baru' => [
						//'availableDate' => ['23/4', '25/4', '26/4'],
					],
					'Bayan Lepas' => [
						//'availableDate' => ['26/4'],
					],
					'Bukit Mertajam' => [
						//'availableDate' => ['24/4'],
					],
					'Butterworth' => [
						//'availableDate' => ['25/4'],
					],
					'Farlim' => [
						//'availableDate' => ['23/4', '26/4'],
					],
					'Gelugor' => [
						//'availableDate' => ['23/4'],
					],
					'Georgetown' => [
						//'availableDate' => ['23/4', '24/4', '26/4'],
					],
					'Greenlane' => [
						//'availableDate' => ['23/4'],
					],
					'Jelutong' => [
						//'availableDate' => ['23/4', '24/4', '26/4'],
					],
					'Paya Terubong' => [
						//'availableDate' => ['23/4', '26/4'],
					],
					'Pulau Tikus' => [
						//'availableDate' => ['23/4', '26/4'],
					],
					'Relau' => [
						//'availableDate' => ['23/4', '25/4'],
					],
					'Simpang Ampat, Juru' => [
						//'availableDate' => ['24/4'],
						
					],
					'Sungai Ara' => [
						//'availableDate' => ['23/4', '25/4', '26/4'],
					],
					'Tanjung Bungah' => [
						//'availableDate' => ['26/4'],
					],
					'Tanjung Tokong' => [
						//'availableDate' => ['26/4'],
					],
				],
			];
		} else if ($tenantId == 11) {
			// viviandeli
			return [
				'options' => [
					'Self Pickup' => [
						'charges' => 0,
						'hideAddress' => true, 
						'selfPickup' => true,
					],
					'Delivery' => [
						'charges' => 0,
					],
				],
				'cities' => [ 
					'Abadi Heights Puchong' => [
						'charges' => 0, 
					],

					'Bandar Kinrara 1-6' => [
						'charges' => 0, 
					],
					'Bandar Puteri Puchong' => [
						'charges' => 0, 
					],

					'Bukit Puchong' => [
						'charges' => 0, 
					],
					'D\'island Residence' => [
						'charges' => 0, 
					],
					'Meranti Jaya' => [
						'charges' => 0, 
					],
					'Puchong Intan' => [
						'charges' => 0, 
					],

					'Puchong Jaya' => [
						'charges' => 0, 
					],

					'Puchong Utama' => [
						'charges' => 0, 
					],

					'Puchong Perdana' => [
						'charges' => 0, 
					],

					'Puchong Prima' => [
						'charges' => 0, 
					],

					'Pusat Bandar Puchong' => [
						'charges' => 0, 
					],
					'Putra Perdana Puchong' => [
						'charges' => 0, 
					],
					'Saujana Puchong' => [
						'charges' => 0, 
					],
					'Taman Industri Puchong' => [
						'charges' => 0, 
					],
					'Taman Puchong Hartamas' => [
						'charges' => 0, 
					],

					'Taman Putra Prima' => [
						'charges' => 0, 
					],
					'Taman Wawasan' => [
						'charges' => 0, 
					],

					'Tasik Prima Puchong' => [
						'charges' => 0, 
					],
				],
			];
		} else if ($tenantId == 12) {
			// Soon Kee Roasted Duck
			return [
				'cities' => [
					'Ampang' => [
						'charges' => 10, 
					],
					'Balakong' => [
						'charges' => 5, 
					],
					'Bukit Jalil' => [
						'charges' => 5, 
					],
					'Cheras' => [
						'charges' => 5, 
					],
					'Damansara' => [
						'charges' => 10, 
					],
					'Gombak' => [
						'charges' => 10, 
					],
					'Kepong' => [
						'charges' => 10, 
					],
					
					'Old Klang Road' => [
						'charges' => 5, 
					],
					'Seri Kembangan' => [
						'charges' => 5, 
					],
					'Setapak' => [
						'charges' => 10, 
					],
					'Shah Alam' => [
						'charges' => 10, 
					],
					'Sri Petaling' => [
						'charges' => 5, 
					],
					'Sungai Buloh' => [
						'charges' => 10, 
					],
					'Usj Subang' => [
						'charges' => 5, 
					],
					'Petaling Jaya' => [
						'charges' => 5, 
					],
					'Puchong' => [
						'charges' => 0, 
					],
					'Putra Height' => [
						'charges' => 5, 
					],
					'Other' => [
						'charges' => false,
					],
				]
			];
		} else if ($tenantId == 13) {
			return [
				'restrictDeliveryDate' => true,
				'cities' => [
					'Balakong' => [
						'availableDay' => [4],
					],
					'Bandar Kinrara' => [
						'availableDay' => [3],
					],
					'Bukit Jalil' => [
						'availableDay' => [3],
					],
					'Cheras' => [
						'availableDay' => [1],
					],
					'Damansara' => [
						'availableDay' => [2],
					],
					'Kajang' => [
						'availableDay' => [1],
					],
					'Kepong' => [
						'availableDay' => [6],
					],
					'Jinjang' => [
						'availableDay' => [6],
					],
					'Mont Kiara' => [
						'availableDay' => [2],
					],
					'Petaling Jaya' => [
						'availableDay' => [5],
					],
					'Puchong' => [
						'availableDay' => [3],
					],
					'Selayang' => [
						'availableDay' => [6],
					],
					'Semenyih' => [
						'availableDay' => [1],
					],
					'Serdang' => [
						'availableDay' => [4],
					],
					'Seri Kembangan' => [
						'availableDay' => [4],
					],
					'Sungai Long' => [
						'availableDay' => [1],
					],
					'Sunway' => [
						'availableDay' => [1],
					],
					'Sri Petaling' => [
						'availableDay' => [3],
					],

					'Sunway' => [
						'availableDay' => [5],
					],
				],
			];
				
		} else if ($tenantId == 14) {
			// Nak.ed farm
			
			return [
				'options' => [
					'West Malaysia' => [
						'charges' => 10,
					],
					'East Malaysia' => [
						'charges' => 15,
					],
				],
			];
		} else if ($tenantId == 17) {
			// Vegeboys
			
			return [
				'rules' => [
					'cartTotal' => new \yii\web\JsExpression('
						function(config, context) {
							var subtotal = context.cart.getSubtotal();
							console.log(subtotal);
							if (subtotal >= config.total) {
								return config.charges;
							}
						}
					'),
				],
				'options' => [
					'Self Pickup / Self Arrangement' => [
						'charges' => 0,
						'hideAddress' => true, 
						'selfPickup' => true,
						'note' => 'For self arrangement, you may check with Lalamove/Grab or other delivery platform for delivery charges.',
					],
					'Delivery' => [
						'charges' => [
							[
								'rule' => 'cartTotal',
								'total' => 100,
								'charges' => 0,
							],
							[
								'rule' => 'cartTotal',
								'total' => 0,
								'charges' => 10,
							]
						],
					],
				],
				'cities' => [
					'Kuala Lumpur' => [
						//'charges' => 10,
					],
					'Petaling Jaya' => [
						//'charges' => 10,
					],
					'Subang' => [
						//'charges' => 10,
					],
					'Puchong' => [
						//'charges' => 10,
					],
				],
			];
		}
	}
}