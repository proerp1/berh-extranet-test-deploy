<?php 
App::uses('AuthComponent', 'Controller/Component');
class CustomerDiscountsProduct extends AppModel {
	public $name = 'CustomerDiscountsProduct';
	
	public $belongsTo = array(
		'CustomerDiscount',
		'Product' => array(
		  'className' => 'Product'
		)
	);

	public function beforeFind($queryData) {		 
		$queryData['conditions'][] = array('CustomerDiscountsProduct.data_cancel' => '1901-01-01 00:00:00');

	  return $queryData;
	}

	public $validate = array(
		'product_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Ocorreu algum erro, tente novamente'
			)
		),
		'discount_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Ocorreu algum erro, tente novamente'
			)
		)
	);
}