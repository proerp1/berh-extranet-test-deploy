<?php 
App::uses('AuthComponent', 'Controller/Component');
class EmailsCampanha extends AppModel {
	public $name = 'EmailsCampanha';
	public $useTable = 'email_campanha';

	public $recursive = -1;
	public $hasMany = array(
    'MailList' => array(
      'foreignKey' => 'email_campanha_id',
			'conditions' => array('MailList.data_cancel' => '1901-01-01 00:00:00')
    ),
   );
	
	public function beforeFind($queryData) {
		$queryData['conditions'][] = array('EmailsCampanha.data_cancel' => '1901-01-01 00:00:00');

		return $queryData;
	}

	public $validate = array(
		'subject' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		)
	);
}