<?php 
App::uses('AuthComponent', 'Controller/Component');
class CustomerUser extends AppModel {
	public $name = 'CustomerUser';
	
	public $belongsTo = array(
		'Customer' => array(
		  //'conditions' => array('CustomerUser.resale' => 0, 'CustomerUser.seller' => 0)
		),
		'Resale' => array(
		  'className' => 'Resale',
		  'foreignKey' => 'customer_id',
		  //'conditions' => array('CustomerUser.resale' => 1)
		),
		'Seller' => array(
		  'className' => 'Seller',
		  'foreignKey' => 'customer_id',
		  //'conditions' => array('CustomerUser.seller' => 1)
		),
		'Status' => array(
		  'className' => 'Status',
		  'foreignKey' => 'status_id',
		  'conditions' => array('Status.categoria' => 1)
		)
	);

	public $validate = array(
		'email' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'O e-mail deve ser válido'
			),
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'O e-mail fornecido já foi cadastrado'
			)
		),
		'password' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatória'
			)
		),
		'name' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'status_id' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		)
	);

	public function beforeFind($queryData) {
		$queryData['conditions'][] = array('CustomerUser.data_cancel' => '1901-01-01 00:00:00');
	  
	  return $queryData;
	}

	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}
}