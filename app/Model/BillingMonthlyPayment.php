<?php 
App::uses('AuthComponent', 'Controller/Component');
class BillingMonthlyPayment extends AppModel {
	public $name = 'BillingMonthlyPayment';

	public $belongsTo = array(
		'Billing',
		'Customer',
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 3)
		)
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('BillingMonthlyPayment.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

	public function afterFind($results, $primary = false){
    foreach ($results as $key => $val) {
      if (isset($val['BillingMonthlyPayment']['monthly_value'])) {
        $results[$key]['BillingMonthlyPayment']['monthly_value_formatado'] = number_format($results[$key]['BillingMonthlyPayment']['monthly_value'],2,',','.');
      }
    }

    return $results;
  }

  public function update_monthly_value_total($billing_id) {
    $result = $this->query("UPDATE billing_monthly_payments SET monthly_value_total = monthly_value, balance_available = monthly_value, billing_quantity = 0 WHERE billing_id = ".$billing_id."");

    return $result;
  }

  public function update_mensalidade_final($billing_id) {
  	//se mensalidade 0,00 e apenas com produtos de pefin e taxa bancaria zera fatura
    $result = $this->query("UPDATE billing_monthly_payments b 
							INNER JOIN billing_nova_vida bn ON bn.billing_id = b.billing_id AND bn.customer_id = b.customer_id AND bn.data_cancel = '1901-01-01'
							SET b.pefin_maintenance_id = NULL, b.monthly_value_total = 0, bn.data_cancel = NOW() 
							WHERE b.billing_id = ".$billing_id." 
							AND b.data_cancel = '1901-01-01'
							AND b.monthly_value = 0 AND b.monthly_value_total = 2.5");

    return $result;

  }
}