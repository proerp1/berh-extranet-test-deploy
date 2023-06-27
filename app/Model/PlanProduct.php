<?php 
App::uses('AuthComponent', 'Controller/Component');
class PlanProduct extends AppModel {
	public $name = 'PlanProduct';

	public $belongsTo = array(
		'Plan' => array(
			'conditions' => array('Plan.status_id' => '1', 'Plan.data_cancel' => '1901-01-01 00:00:00')
		),
		'Product' => array(
			'conditions' => array('Product.status_id' => '1', 'Product.data_cancel' => '1901-01-01 00:00:00')
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('PlanProduct.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public $validate = array(
    'product_id' => array(
      'required' => array(
        'rule' => array('notEmpty'),
        'message' => 'Campo obrigatório'
      )
    ),
    'gratuidade' => array(
      'required' => array(
        'rule' => array('notEmpty'),
        'message' => 'Campo obrigatório'
      )
    )
  );
}