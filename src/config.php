<?php

return [
    'id' => 'shipping',
	'namespace' => 'ant\shipping',
    'class' => \ant\shipping\Module::className(),
    'isCoreModule' => false,
	'modules' => [
		//'v1' => \ant\ecommerce\api\v1\Module::class,
		'backend' => \ant\shipping\backend\Module::class,
	],
	'depends' => [],
];