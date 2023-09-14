<?php 
App::uses('AuthComponent', 'Controller/Component');
class User extends AppModel {
	public $name = 'User';
	
	public $belongsTo = array(
		'Group' => array(
			'className' => 'Group'
		),
		'Status' => array(
		  'className' => 'Status',
		  'foreignKey' => 'status_id',
		  'conditions' => array('Status.categoria' => 1)
		)
	);

	public $validate = array(
		'username' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'O e-mail deve ser válido'
			),
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'O e-mail é obrigatório'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'O e-mail fornecido já foi cadastrado'
			)
		),
		'password' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'A senha é obrigatória'
			)
		),
		'name' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'O nome é obrigatório'
			)
		)
	);

	public function beforeFind($queryData) {
		if (CakeSession::read("Auth.User.Group.id") != 1 && CakeSession::read("Auth.User.estado")) {
		  $queryData['conditions'][] = array('User.data_cancel' => '1901-01-01 00:00:00', 'User.estado' => CakeSession::read("Auth.User.estado"));
		} else {
		  $queryData['conditions'][] = array('User.data_cancel' => '1901-01-01 00:00:00');
		}
	  
	  return $queryData;
	}

	public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['sales_goal']) && !isset($val[$this->alias]['sales_goal_not_formated'])) {
                $results[$key][$this->alias]['sales_goal_not_formated'] = $results[$key][$this->alias]['sales_goal'];
                $results[$key][$this->alias]['sales_goal'] = number_format($results[$key][$this->alias]['sales_goal'], 2, ',', '.');

            }
        }

        return $results;
    }

	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['sales_goal'])) {
            $this->data[$this->alias]['sales_goal'] = $this->priceFormatBeforeSave($this->data[$this->alias]['sales_goal']);
        }

		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password'], null, true);
		}
		return true;
	}

	public function priceFormatBeforeSave($price)
    {
        $valueFormatado = str_replace('.', '', $price);
        $valueFormatado = str_replace(',', '.', $valueFormatado);

        return $valueFormatado;
    }
}