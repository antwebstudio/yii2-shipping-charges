<?php
namespace ant\shipping\backend\controllers;

use Yii;
use ant\shipping\models\ShippingRule;

class ShippingRuleController extends \yii\web\Controller {
	public function actionIndex() {
		$dataProvider = new \yii\data\ActiveDataProvider([
			'query' => ShippingRule::find(),
		]);
		return $this->render($this->action->id, [
			'dataProvider' => $dataProvider,
		]);
	}
	
	public function actionBasic() {
		$tenant = \ant\tenant\models\Tenant::getCurrent();
		$model = ShippingRule::find()->one();
		$customized = ShippingRule::find()->count() > 1;
		
		if (!isset($model)) {
			$model = new ShippingRule;
			$model->rule_class_id = \ant\models\ModelClass::getClassId(\ant\shipping\rules\FreeWhenCartSubtotal::class);
		} else if ($model->rule_class_id != \ant\models\ModelClass::getClassId(\ant\shipping\rules\FreeWhenCartSubtotal::class)) {
			$customized = true;
		}
		
		if (isset($tenant->config['shippingCharges'][0]['class']) && $tenant->config['shippingCharges'][0]['class'] != \ant\shipping\calculators\DbRules::class) {
			$customized = true;
		}
		
		$model->extraAttributeLabels = [
			'option[default]' => 'Delivery Charges',
			'option[total]' => 'Total',
		];
		
		$model->extraRules = [
			['option', \ant\validators\SerializableDataValidator::class, 'rules' => [
				['default', 'required', 'when' => function() { return false; }],
				['total', 'required', 'when' => function() { return false; }],
			]],
		];
		
		if ($model->load($_POST)) {
			$model->option = json_encode($model->option);
			
			if ($model->save()) {
				Yii::$app->session->setFlash('success', 'Delivery charges is successfully updated. ');
				return $this->refresh();
			}
		}
		
		return $this->render($this->action->id, [
			'model' => $model,
			'customized' => $customized,
		]);
	}
	
	public function actionCreate() {
		$model = new ShippingRule;
		
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['update', 'id' => $model->id]);
		}
		
		return $this->render($this->action->id, [
			'model' => $model,
		]);
	}
	
	public function actionUpdate($id) {
		$model = ShippingRule::findOne($id);
		
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['index']);
		}
		
		return $this->render($this->action->id, [
			'model' => $model,
		]);
	}
	
	public function actionDelete($id) {
		$model = ShippingRule::findOne($id);
		
		$model->delete();
		
		return $this->redirect(['index']);
	}
}