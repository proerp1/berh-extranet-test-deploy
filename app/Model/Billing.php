<?php 
App::uses('AuthComponent', 'Controller/Component');
class Billing extends AppModel {
	public $name = 'Billing';
	public $displayField = "date_billing";


	public $belongsTo = array(
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 1)
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('Billing.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function afterFind($results, $primary = false){
		setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8');
    foreach ($results as $key => $val) {
      if (isset($val['Billing']['date_billing'])) {
        $results[$key]['Billing']['date_billing_nao_formatado'] = $val['Billing']['date_billing'];
        $results[$key]['Billing']['date_billing'] = date("d/m/Y", strtotime($val['Billing']['date_billing']));
        $results[$key]['Billing']['date_billing_index'] = ucfirst(strftime('%B de %Y', strtotime($val['Billing']['date_billing'])));
      }
    }

    return $results;
  }

	public function beforeSave($options = array()) {
		if (!empty($this->data['Billing']['date_billing'])) {
			$this->data['Billing']['date_billing'] = $this->date_converter($this->data['Billing']['date_billing']);
		}

		return true;
	}

	public function date_converter($_date = null) {
    $format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
    if ($_date != null && preg_match($format, $_date, $partes)) {
      return $partes[3].'-'.$partes[2].'-'.$partes[1];
    }
    
    return false;
  }

	public $validate = array(
		'date_billing' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Campo obrigatório'
			)
		)
	);
}