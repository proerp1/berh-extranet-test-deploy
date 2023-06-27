<?php 
App::uses('AuthComponent', 'Controller/Component');
class SocioMeProteja extends AppModel {
	public $name = 'SocioMeProteja';
	public $useTable = 'sociosMeProteja';
	public $primaryKey = 'socioMeProtejaID';


	public $belongsTo = array(
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'clienteID'
		),
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('SocioMeProteja.socioMeProtejaDataCancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public $validate = array(
		'socioMeProtejaNome' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'socioMeProtejaEmail' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'socioMeProtejaCelular' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'socioMeProtejaTipoDoc' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		),
		'socioMeProtejaDoc' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'Campo obrigatório'
			)
		)
	);

	public function find_socios_fila($doc){

		$sql = "SELECT s.socioMeProtejaID, s.socioMeProtejaEmail, s.socioMeProtejaCelular, s.socioMeProtejaNome, c.id as customerid
				FROM sociosMeProteja s
					INNER JOIN customers c ON c.id = s.clienteID
					INNER JOIN cronMeProteja cr ON cr.clienteID = c.id
				WHERE s.socioMeProtejaDataCancel = '1901-01-01' AND c.data_cancel = '1901-01-01' AND cr.cronMeProtejaDataCancel = '1901-01-01' 
					AND SUBSTR(REPLACE(REPLACE(REPLACE(s.socioMeProtejaDoc, '.', ''), '-', ''), '/', ''), 1, 9) LIKE '%".$doc."%' ";
		
		$result = $this->query($sql);
		return $result;
	}

	public function update_cancel_socio($id){
		$sql = "UPDATE sociosMeProteja 
				SET usuarioIDCancel = 99999, socioMeProtejaDataCancel = '".date("Y-m-d H:i:s")."'
				WHERE socioMeProtejaID = $id ";

		$this->query($sql);
	}
}
