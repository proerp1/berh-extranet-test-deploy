<?php 
App::uses('AuthComponent', 'Controller/Component');
class BillingNovaVida extends AppModel {
	public $name = 'BillingNovaVida';
	public $useTable = 'billing_nova_vida';

	public $belongsTo = array(
		'Billing',
		'Customer',
		'Product'
	);

	public function beforeFind($queryData) {

		$queryData['conditions'][] = array('BillingNovaVida.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}
}