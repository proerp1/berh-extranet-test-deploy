<?php 
App::uses('AuthComponent', 'Controller/Component');
class Seller extends AppModel {
	public $name = 'Seller';
	public $displayField = 'nome_fantasia';

	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 1)
		),
		'Resale'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Seller.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function beforeSave($options = array()) {
		if (!empty($this->data['Plan']['value'])) {
			$this->data['Plan']['value'] = $this->priceFormatBeforeSave($this->data['Plan']['value']);
		}
		
		return true;
	}

	public function priceFormatBeforeSave($price) {
		$valueFormatado = str_replace('.', '', $price);
		$valueFormatado = str_replace(',', '.', $valueFormatado);

		return $valueFormatado;
	}

	public function afterFind($results, $primary = false){
		foreach ($results as $key => $val) {
			if (isset($val['Plan']['value'])) {
				$results[$key]['Plan']['value'] = number_format($results[$key]['Plan']['value'],2,',','.');
			}
		}

		return $results;
	}

	public $validate = array(
		'nome_fantasia' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'documento' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'O documento é obrigatório',
				'last' => false
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'O documento fornecido já foi cadastrado.'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'O e-mail deve ser válido',
				'last' => false
			),
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'O e-mail é obrigatório'/*,
				'last' => false*/
			)/*,
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'O e-mail fornecido já foi cadastrado.'
			)*/
		),
		'cep' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'O cep é obrigatório'
			),
		),
		'endereco' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'O endereço é obrigatório'
			),
		),
		'numero' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'O numero é obrigatório',
				'last' => false
			),
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Somente números'
			),
		),
		'bairro' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'O bairro é obrigatório'
			),
		),
		'cidade' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'A cidade é obrigatória'
			),
		),
		'estado' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'O estado é obrigatório'
			),
		),
		'tipo_comissao' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo é obrigatório'
			),
		),
		'valor_comissao' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo é obrigatório'
			),
		)
	);
}