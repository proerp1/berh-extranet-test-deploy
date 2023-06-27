<?php 
App::uses('AuthComponent', 'Controller/Component');
class DistribuicaoCobrancaUsuario extends AppModel {
	public $name = 'DistribuicaoCobrancaUsuario';
	public $useTable = 'distribuicao_cobranca_usuarios';

	public $belongsTo = array(
		'User' => ['order' => ['User.name' => 'asc']],
		'Income' => ['conditions' => ['Income.data_cancel' => '1901-01-01 00:00:00']],
		'DistribuicaoCobranca'
	);
	
	public function beforeFind($queryData) {
		$queryData['conditions'][] = array('DistribuicaoCobrancaUsuario.data_cancel' => '1901-01-01 00:00:00');

		return $queryData;
	}
}