<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use ant\models\ModelClass;

$ruleOptions = [
	'ant\shipping\calculators\Zero' => null,
	'ant\shipping\calculators\ToBeConfirmed' => null,
	'ant\shipping\rules\FreeWhenCartSubtotal' => ['total' => 0],
	'ant\shipping\rules\FreeWhenCartTotalQuantity' => ['total' => 0, 'categories' => null],
	'ant\shipping\rules\FlatRate' => ['price' => 5],
];

$dropDown = [];
foreach ($ruleOptions as $className => $option) {
	//$className = $option;
	$dropDown[ModelClass::getClassId($className)] = $className;
}

if (!$model->isNewRecord && trim($model->option) == '') {
	$model->option = json_encode($ruleOptions[$model->ruleClass->class_name], JSON_PRETTY_PRINT);
}

?>

<?php $form = ActiveForm::begin() ?>
	<?php if ($model->isNewRecord): ?>
		<?= $form->field($model, 'rule_class_id')->dropDownList($dropDown, ['prompt' => '']) ?>
	<?php else: ?>
		  <div class="form-group row">
			<label class="col-sm-2 col-form-label">Class</label>
			<div class="col-sm-10">
			  <input type="text" readonly class="form-control-plaintext" value="<?= $model->ruleClass->class_name ?>">
			</div>
		  </div>
	<?php endif ?>
	<?= $form->field($model, 'priority')->textInput(['type' => 'number']) ?>
	
	
	<?php if (!$model->isNewRecord): ?>
		<?= $form->field($model, 'option')->widget(\trntv\aceeditor\AceEditor::class, [
			'mode' => 'json',
		]) ?>
	<?php endif ?>
	
	<?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end() ?>