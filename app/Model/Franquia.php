<?php 
App::uses('AuthComponent', 'Controller/Component');
class Franquia extends AppModel {
	public $name = 'Franquia';
	public $useTable = 'franquia';
	public $displayField = 'nome_fantasia';

	public $validate = array(
		'nome_fantasia' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'O nome é obrigatório'
			)
		)
	);
}