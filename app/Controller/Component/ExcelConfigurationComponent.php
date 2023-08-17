<?php

class ExcelConfigurationComponent extends Component {

	public function getConfiguration($model)
	{
		$arr = [
			'OrderItem'	=> [
				'limit' => 20, 'order' => ['OrderItem.id' => 'desc'],
				'fields' => [
					'Customer.nome_primario',
					'Customer.documento',
					'CustomerUser.*',
					'CustomerDepartment.name',
					'CustomerUserItinerary.*',
					'OrderItem.*',
					'Benefit.code',
					'Supplier.code',
					'MAX(CustomerUserAddress.zip_code) as cep',
					'MAX(CustomerUserAddress.address_line) as endereco',
					'MAX(CustomerUserAddress.address_number) as numero',
					'MAX(CustomerUserAddress.address_complement) as complemento',
					'MAX(CustomerUserAddress.neighborhood) as bairro',
					'MAX(CustomerUserAddress.city) as cidade',
					'MAX(CustomerUserAddress.state) as estado',
					'MAX(CustomerAddress.zip_code) as cep_empresa',
					'MAX(CustomerAddress.address_line) as endereco_empresa',
					'MAX(CustomerAddress.address_number) as numero_empresa',
					'MAX(CustomerAddress.address_complement) as complemento_empresa',
					'MAX(CustomerAddress.neighborhood) as bairro_empresa',
					'MAX(CustomerAddress.city) as cidade_empresa',
					'MAX(CustomerAddress.state) as estado_empresa',

					'MAX(CustomerUserBankAccount.account_type_id) as tipo_conta',
					'MAX(CustomerUserBankAccount.bank_name) as nome_banco',
					'MAX(CustomerUserBankAccount.bank_code) as codigo_banco',
					'MAX(CustomerUserBankAccount.acc_number) as numero_conta',
					'MAX(CustomerUserBankAccount.acc_digit) as digito_conta',
					'MAX(CustomerUserBankAccount.branch_number) as numero_agencia',
					'MAX(CustomerUserBankAccount.branch_digit) as digito_agencia',

					'CustomerPosition.name',
					'SalaryRange.range',
					'MaritalStatus.status',
					
				],
				'joins' => [
					[
						'table' => 'orders',
						'alias' => 'Order',
						'type' => 'INNER',
						'conditions' => ['Order.id = OrderItem.order_id']
					],
					[
						'table' => 'customers',
						'alias' => 'Customer',
						'type' => 'INNER',
						'conditions' => ['Customer.id = Order.customer_id']
					],
					[
						'table' => 'customer_users',
						'alias' => 'CustomerUser',
						'type' => 'INNER',
						'conditions' => ['CustomerUser.id = OrderItem.customer_user_id']
					],
					[
						'table' => 'customer_departments',
						'alias' => 'CustomerDepartment',
						'type' => 'LEFT',
						'conditions' => ['CustomerDepartment.id = CustomerUser.customer_departments_id']
					],
					[
						'table' => 'cost_center',
						'alias' => 'CostCenter',
						'type' => 'LEFT',
						'conditions' => ['CostCenter.id = CustomerUser.customer_departments_id']
					],
					[
						'table' => 'customer_user_itineraries',
						'alias' => 'CustomerUserItinerary',
						'type' => 'INNER',
						'conditions' => ['CustomerUserItinerary.id = OrderItem.customer_user_itinerary_id']
					],
					[
						'table' => 'benefits',
						'alias' => 'Benefit',
						'type' => 'INNER',
						'conditions' => ['Benefit.id = CustomerUserItinerary.benefit_id']
					],
					[
						'table' => 'suppliers',
						'alias' => 'Supplier',
						'type' => 'INNER',
						'conditions' => ['Supplier.id = Benefit.supplier_id']
					],
					[
						'table' => 'customer_user_addresses',
						'alias' => 'CustomerUserAddress',
						'type' => 'LEFT',
						'conditions' => ['CustomerUserAddress.customer_user_id = CustomerUser.id']
					],
					[
						'table' => 'customer_addresses',
						'alias' => 'CustomerAddress',
						'type' => 'LEFT',
						'conditions' => ['CustomerAddress.customer_id = Customer.id']
					],
					[
						'table' => 'customer_user_bank_accounts',
						'alias' => 'CustomerUserBankAccount',
						'type' => 'LEFT',
						'conditions' => ['CustomerUserBankAccount.customer_user_id = CustomerUser.id']
					],
					[
						'table' => 'customer_positions',
						'alias' => 'CustomerPosition',
						'type' => 'LEFT',
						'conditions' => ['CustomerPosition.id = CustomerUser.customer_positions_id']
					],
					[
						'table' => 'marital_statuses',
						'alias' => 'MaritalStatus',
						'type' => 'LEFT',
						'conditions' => ['MaritalStatus.id = CustomerUser.marital_status_id']
					],
					[
						'table' => 'salary_ranges',
						'alias' => 'SalaryRange',
						'type' => 'LEFT',
						'conditions' => ['SalaryRange.id = CustomerUser.customer_salary_id']
					]
				],
				'group' => [
					'OrderItem.id'
				],
				'recursive' => -1,
			]
		];

        return $arr[$model];
	}
}