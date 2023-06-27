<?php 
App::uses('AuthComponent', 'Controller/Component');
class LogBillingSale extends AppModel {
	public $name = 'LogBillingSale';

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		'Billing' => array(
			'className' => 'Billing',
			'foreignKey' => 'billing_id'
		),
		'Seller' => array(
			'className' => 'Seller',
			'foreignKey' => 'seller_id'
		),
		'Resaler' => array(
			'className' => 'Resaler',
			'foreignKey' => 'resaler_id'
		),
		'BillingSale' => array(
			'className' => 'BillingSale',
			'foreignKey' => 'period_id'
		)
	);















}
