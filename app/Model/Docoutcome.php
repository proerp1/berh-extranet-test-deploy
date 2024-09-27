<?php 
App::uses('AuthComponent', 'Controller/Component');
class Docoutcome extends AppModel {
	public $name = 'Docoutcome';

	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 1)
		),
		'Outcome',
		'TipoDocumento' => array(
        'className' => 'TipoDocumento',
        'foreignKey' => 'tipo_documento_id'
    ),
	);

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