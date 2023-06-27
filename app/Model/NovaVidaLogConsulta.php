<?php 
App::uses('AuthComponent', 'Controller/Component');
class NovaVidaLogConsulta extends AppModel {
	public $name = 'NovaVidaLogConsulta';

	public $belongsTo = array(
		'CustomerUser',
		'Customer',
		'Product',
		'PlanCustomer'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('NovaVidaLogConsulta.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function sp_log_consulta($clienteID, $usuarioID, $produtoID, $documento){

		$sql = "CALL sp_log_consulta(".$clienteID.", ".$usuarioID.", ".$produtoID.", '".$documento."'); ";
		$exSql = $this->query($sql);

		return $exSql[0][0];
	}
}