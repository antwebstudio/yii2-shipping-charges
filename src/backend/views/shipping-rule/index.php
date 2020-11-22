<?php
use yii\helpers\Url;
use yii\grid\GridView;
use ant\grid\ActionColumn;
?>

<a class="btn btn-primary" href="<?= Url::to(['create']) ?>">New Rule</a>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'columns' => [
		'tenant.handle',
		'ruleClass.class_name',
		'priority',
		[
			'attribute' => 'option',
			'format' => 'html',
			'value' => function($model) {
				return '<pre>'.json_encode(json_decode($model->option), JSON_PRETTY_PRINT).'</pre>';
			}
		],
		[
			'class' => ActionColumn::class,
		],
	],
]) ?>