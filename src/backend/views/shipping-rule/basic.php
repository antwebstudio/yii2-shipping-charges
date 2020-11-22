<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$checkboxId = 'freeWhen';
$tbcCheckboxId = 'tbc';
$totalInputId = 'total';
$defaultInputId = 'default';

$model->option = json_decode($model->option, true);
$isChecked = isset($model->option['total']) && $model->option['total'];
$isTbcChecked = !$model->isNewRecord && !isset($model->option['default']);
?>

<?php if (YII_DEBUG): ?>
	<?php $tenant = \ant\tenant\models\Tenant::getCurrent() ?>
	<?php if (isset($tenant)): ?>
		Tenant shipping charges config:
		<pre><?= print_r($tenant->config['shippingCharges'], 1) ?></pre>
	<?php endif ?>
	Rule: <?= $model->ruleClass->class_name ?>
	<pre><?= print_r($model->attributes, 1) ?></pre>
<?php endif ?>

<?php if (!$customized): ?>

	<?php $form = ActiveForm::begin(['fieldClass' => ant\widgets\ActiveField::class]) ?>
		<?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
		
		<?php // This is needed so that when option is null, Yii::$app->request->post() is true ?>
		<?= $form->field($model, 'option[]')->hiddenInput()->label(false) ?>
		
		<?= $form->field($model, 'option[default]')->textInput(['id' => $defaultInputId, 'type' => 'number'])->label($model->getAttributeLabel('option[default]')) ?>
		
		<div>
			<?= Html::checkbox('tbc', $isTbcChecked, ['id' => $tbcCheckboxId]) ?>
			<?= Html::label('To be confirmed', $tbcCheckboxId) ?>
		</div>
		
		<div>
			<?= Html::checkbox('freeWhen', $isChecked, ['id' => $checkboxId]) ?>
			<?= Html::label('Free when total amount for purchased products is greater than or equals to', $checkboxId) ?>
		</div>
		
		<?= $form->field($model, 'option[total]', [
			'template' => '<div class="input-group" id="'.$totalInputId.'_div"><div class="input-group-prepend">
				<span class="input-group-text">RM</span>
			</div>{input}</div>',
		])->textInput(['disabled' => '', 'style' => 'display: none', 'type' => 'number', 'id' => $totalInputId])->label(false) ?>
		
		<?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
	<?php ActiveForm::end() ?>

	<script>
		(function() {
			var totalInput = document.querySelector('#<?= $totalInputId ?>');
			var totalInputDiv = document.querySelector('#<?= $totalInputId ?>_div');
			var checkbox = document.querySelector('#<?= $checkboxId ?>');
			checkbox.addEventListener('change', function() {
				totalInput.disabled = !this.checked;
				totalInput.style.display = this.checked ? '' : 'none';
				totalInputDiv.style.display = totalInput.style.display;
				if (!this.checked) {
					totalInput.dispatchEvent(new Event('blur'));
				}
			});
			checkbox.dispatchEvent(new Event('change'));
			
			// To be confirmed
			var defaultInput = document.querySelector('#<?= $defaultInputId ?>');
			var checkbox = document.querySelector('#<?= $tbcCheckboxId ?>');
			checkbox.addEventListener('change', function() {
				defaultInput.disabled = this.checked;
				defaultInput.style.display = !this.checked ? '' : 'none';
				if (this.checked) {
					defaultInput.dispatchEvent(new Event('blur'));
				}
			});
			checkbox.dispatchEvent(new Event('change'));
		})();
	</script>
<?php else: ?>
	<div class="alert alert-info">
		Sorry, your delivery charges was setup manually, hence can't be edited here. Please contact 016-4422426 for any changes required.
	</div>
<?php endif ?>