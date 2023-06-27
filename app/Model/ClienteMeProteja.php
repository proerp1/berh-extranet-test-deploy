<?php 
App::uses('AuthComponent', 'Controller/Component');
class ClienteMeProteja extends AppModel {
	public $name = 'ClienteMeProteja';
	public $useTable = 'clienteMeProteja';
	public $primaryKey = 'clienteMeProtejaID';

	public $belongsTo = array(
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'clienteID'
		),
		'BillingMonthlyPayment' => array(
			'className' => 'BillingMonthlyPayment',
			'foreignKey' => 'billingMonthlyPaymentID'
		),
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'productID'
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('ClienteMeProteja.clienteMeProtejaDataCancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function find_cliente_me_proteja($clienteID, $balanceID){
		$sql = "SELECT c.clienteMeProtejaID, p.model, c.clienteMeProtejaDias, c.clienteMeProtejaValor
							FROM clienteMeProteja c
								INNER JOIN product p ON p.productid = c.productID 
							WHERE c.clienteMeProtejaDataCancel = '1901-01-01' AND p.data_cancel = '1901-01-01' AND c.clienteID = ".$clienteID." 
										AND c.balanceID = ".$balanceID."
							ORDER BY c.clienteMeProtejaDataCadastro";
		$exSql = $this->query($sql);

		if ($exSql) {
			return $exSql;
		} else {
			return false;
		}
	}

	public function find_clientes_historico($clienteID){
		$sql = "SELECT c.clienteMeProtejaID,
						       COUNT(c.clienteMeProtejaID)                                                     AS qtde,
						       SUM(c.clienteMeProtejaValor)                                                    AS valor,
						       SUM(c.clienteMeProtejaDias)                                                     AS dias,
						       c.clienteMeProtejaValidade,
						       CONCAT('<b>CNPJ da sua empresa</b>: ', cl.documento, '<br>', '<b>E-mail</b>: -<br>') AS cliente,
						       (SELECT GROUP_CONCAT('<b>Sócio</b>: ', s.socioMeProtejaNome SEPARATOR '<br>')
						        FROM sociosMeProteja s
						        WHERE s.clienteID = cl.id
						          AND s.socioMeProtejaDataCancel = '1901-01-01')                               AS socio,
						       'Me Proteja'                                                                    AS plano
						FROM clienteMeProteja c
						         INNER JOIN customers cl ON cl.id = c.clienteID
						WHERE c.clienteMeProtejaDataCancel = '1901-01-01'
						  AND cl.data_cancel = '1901-01-01'
						  AND c.clienteID = {$clienteID}
						ORDER BY c.clienteMeProtejaID DESC";

		$result = $this->query($sql);
	
		return $result;
	}

	public function find_clientes_fila($doc){
		$sql = "SELECT c.id as 'customerid', cr.cronMeProtejaID
				FROM customers c
					INNER JOIN cronMeProteja cr ON cr.clienteID = c.id
				WHERE c.data_cancel = '1901-01-01' 
					AND cr.cronMeProtejaDataCancel = '1901-01-01' 
					AND SUBSTR(REPLACE(REPLACE(REPLACE(c.documento, '.', ''), '-', ''), '/', ''), 1, 9) LIKE '%".$doc."%' ";
		
		$result = $this->query($sql);
		return $result;
	}
}