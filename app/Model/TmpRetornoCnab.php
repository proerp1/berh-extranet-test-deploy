<?php 
App::uses('AuthComponent', 'Controller/Component');
class TmpRetornoCnab extends AppModel {
	public $name = 'TmpRetornoCnab';
	public $useTable = 'tmp_retorno_cnab';

	public $belongsTo = array(
		'RetornoCnab',
		'Income'
	);
}