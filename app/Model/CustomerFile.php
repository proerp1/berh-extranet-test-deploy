<?php 
App::uses('AuthComponent', 'Controller/Component');
class CustomerFile extends AppModel {
	public $name = 'CustomerFile';
	
	public $belongsTo = array(
		'Customer',
		'Layout',
		'Criado' => [
            'className' => 'CustomerUser',
            'foreignKey' => 'user_creator_id',
        ],
		'Finalizado' => [
            'className' => 'User',
            'foreignKey' => 'user_finalizado_id',
        ],
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 21)
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('CustomerFile.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public $actsAs = [
        'Upload.Upload' => [
            'file' => [
                'rootDir' => ROOT_SITE,
                'path' => '{ROOT}{DS}app{DS}webroot{DS}files{DS}{model}{DS}{field}{DS}',
            ],
        ],
    ];
}
