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
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'O e-mail fornecido já foi cadastrado'
			)
		),
		'password' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatória'
			)
		),
		'name' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		),
		'status_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		)
	);

	public function beforeFind($queryData) {
		$queryData['conditions'][] = array('CustomerUser.data_cancel' => '1901-01-01 00:00:00');
	  
	  return $queryData;
	}

	public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['data_nascimento'])) {
                $results[$key][$this->alias]['data_nascimento_nao_formatado'] = $results[$key][$this->alias]['data_nascimento'];
                $results[$key][$this->alias]['data_nascimento'] = date("d/m/Y", strtotime($results[$key][$this->alias]['data_nascimento']));
            }
        }

        return $results;
    }

	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password']);
		}

		if (!empty($this->data[$this->alias]['data_nascimento'])) {
			$this->data[$this->alias]['data_nascimento'] = $this->dateFormatBeforeSave($this->data[$this->alias]['data_nascimento']);
		}
		return true;
	}

	public function dateFormatBeforeSave($dateString)
    {
        return date('Y-m-d', strtotime($this->date_converter($dateString)));
    }

    public function date_converter($_date = null)
    {
        $format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
        if ($_date != null && preg_match($format, $_date, $partes)) {
            return $partes[3].'-'.$partes[2].'-'.$partes[1];
        }
        
        return false;
    }
}