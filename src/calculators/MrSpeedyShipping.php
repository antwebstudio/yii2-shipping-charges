<?php
namespace ant\shipping\calculators;

class MrSpeedyShipping extends \ant\shipping\base\ShippingRule {
	public $cart;
	public $shippingPreference;
	public $shipTo;
	public $api = '25B4084A1F98E062473865825268B2B4848ADE07';
	public $clientId = '179749';
	public $matter = 'Food';
	public $shopAddress;
	
	protected $testMode = YII_DEBUG;
	protected $testUrl = 'https://robotapitest.mrspeedy.my/api/business/1.1';
	protected $url = 'https://robot.mrspeedy.my/api/business/1.1';
	
	public function apply() {
		$url = $this->testMode ? $this->testUrl : $this->url;
		
		if (!isset($this->shipTo->address->fullAddressString)) {
			return null;
		}
		
		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_URL, $url.'/calculate-order'); 
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); 
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['X-DV-Auth-Token: '.$this->api]); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		 
		$data = [ 
			'matter' => $this->matter, 
			'points' => [ 
				[ 
					'address' => $this->shopAddress, 
				], 
				[ 
					'address' => $this->shipTo->address->fullAddressString, 
				], 
			], 
		]; 
		 
		$json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json); 
		 
		$result = curl_exec($curl); 
		if ($result === false) { 
			throw new Exception(curl_error($curl), curl_errno($curl)); 
		} 
		$result = json_decode($result, 1);
		
		return isset($result['order']['delivery_fee_amount']) ? $result['order']['delivery_fee_amount'] : false;
		//echo '<pre>'.print_r($result, 1).'</pre>'; 
	}
}
