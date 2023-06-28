<?php 
App::uses('AuthComponent', 'Controller/Component');
class ContasReceberHipercheck extends AppModel {
	public $name = 'ContasReceberHipercheck';
	public $useTable = 'contas_receber_berh';

	public $belongsTo = array(
		'Customer'
	);
}