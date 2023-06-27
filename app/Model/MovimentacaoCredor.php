<?php 
App::uses('AuthComponent', 'Controller/Component');
class MovimentacaoCredor extends AppModel {
	public $name = 'MovimentacaoCredor';
	public $useTable = 'movimentacao_credor';

	public $belongsTo = array(
		'Status' => array(
			'conditions' => array('Status.categoria' => 2)
		),
    'Customer',
    'UserCreated' => array(
			'className' => 'User',
    	'foreignKey' => 'user_created_id'
    )
	);

}