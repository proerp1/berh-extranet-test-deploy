<?php 
App::uses('AuthComponent', 'Controller/Component');
class CadastroPefinLote extends AppModel {
	public $name = 'CadastroPefinLote';
	public $useTable = 'cadastro_pefin_lote';

	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 6)
		)
	);

}