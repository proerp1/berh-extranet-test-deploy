<?php 
App::uses('AuthComponent', 'Controller/Component');
class ProductFeature extends AppModel {
	public $name = 'ProductFeature';

	public $belongsTo = array(
		'Product',
		'NovaVidaFeature' => array(
			'order' => ['NovaVidaFeature.name' => 'asc']
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('ProductFeature.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public $validate = array(
		'nova_vida_feature_id' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		)
	);
}