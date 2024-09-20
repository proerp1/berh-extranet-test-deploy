<?php 
App::uses('AuthComponent', 'Controller/Component');
class Docoutcome extends AppModel {
	public $name = 'Docoutcome';



	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Docoutcome.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public $actsAs = array(
		'Upload.Upload' => array(
			'file'
		)
	);

	public $validate = array(
		'name' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'O nome é obrigatório'
			)
		),
		'status_id' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'O nome é obrigatório'
			)
		)
	);

}