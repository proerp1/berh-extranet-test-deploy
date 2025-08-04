<?php

class ExcelConfigurationComponent extends Component {

	public function getConfiguration($model)
	{
		$arr = [
			'OrderItem'	=> [
				'limit' => 1000000,
				'maxLimit' => 1000000,
				'order' => ['OrderItem.customer_user_id' => 'desc'],
				'fields' => [
					'Customer.nome_primario',
					'Customer.documento',
					'CustomerUser.*',
					'CustomerDepartment.name',
					'CustomerUserItinerary.*',
					'OrderItem.*',
					'Benefit.code',
					'Supplier.id',
					'Supplier.code',
					'Modalidade.name',
					'Tecnologia.name',
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
					'MAX(BankCode.name) as nome_banco',
					'MAX(BankCode.code) as codigo_banco',
					'MAX(CustomerUserBankAccount.acc_number) as numero_conta',
					'MAX(CustomerUserBankAccount.acc_digit) as digito_conta',
					'MAX(CustomerUserBankAccount.branch_number) as numero_agencia',
					'MAX(CustomerUserBankAccount.branch_digit) as digito_agencia',

					'CustomerPosition.name',
					'SalaryRange.range',
					'MaritalStatus.status',

					'Order.id',
					'Order.order_period_from',
					'Order.order_period_to',

					'OrderStatus.name',
                    'Customer.flag_gestao_economico',
                    'Customer.porcentagem_margem_seguranca',
                    'Customer.qtde_minina_diaria',
                    'Customer.tipo_ge',					
				],
				'joins' => [
					[
						'table' => 'orders',
						'alias' => 'Order',
						'type' => 'INNER',
						'conditions' => ['Order.id = OrderItem.order_id']
					],
					[
						'table' => 'statuses',
						'alias' => 'OrderStatus',
						'type' => 'INNER',
						'conditions' => ['Order.status_id = OrderStatus.id']
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
						'conditions' => ['CustomerUserAddress.customer_user_id = CustomerUser.id and CustomerUserAddress.address_type_id = 1']
					],
					[
						'table' => 'customer_user_addresses',
						'alias' => 'CustomerAddress',
						'type' => 'LEFT',
						'conditions' => ['CustomerAddress.customer_user_id = CustomerUser.id and CustomerAddress.address_type_id = 2']
					],
					[
						'table' => 'customer_user_bank_accounts',
						'alias' => 'CustomerUserBankAccount',
						'type' => 'LEFT',
						'conditions' => ['CustomerUserBankAccount.customer_user_id = CustomerUser.id']
					],
					[
						'table' => 'bank_codes',
						'alias' => 'BankCode',
						'type' => 'LEFT',
						'conditions' => ['BankCode.id = CustomerUserBankAccount.bank_code_id']
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
			],
			'OrderItemReportsPedido'	=> [
				'limit' => 1000000,
				'maxLimit' => 1000000,
				'order' => ['OrderItem.customer_user_id' => 'desc'],
				'fields' => [
					'Customer.nome_primario',
					'Customer.documento',
					'CustomerUser.*',
					'CustomerDepartment.name',
					'CustomerUserItinerary.*',
					'OrderItem.*',
					'Benefit.code',
					'BenefitType.name',
					'Supplier.id',
					'EconomicGroups.razao_social',
					'EconomicGroups.document',
					'Supplier.nome_fantasia',
					'Customer.codigo_associado',

					'CustomerPosition.name',
					'SalaryRange.range',
					'MaritalStatus.status',

					'Order.id',
					'Order.credit_release_date',
					'Order.order_period_from',
					'Order.order_period_to',
					'Order.created',
					'Order.transfer_fee',
					'Order.is_partial',
                    'Order.primeiro_pedido',
                    'Order.pedido_complementar',
					'Order.updated_ge',
					'Order.observation_ge',

					'OrderStatus.name',

					'UpdatedGe.name',

                    'Customer.flag_gestao_economico',
                    'Customer.porcentagem_margem_seguranca',
                    'Customer.qtde_minina_diaria',
                    'Customer.tipo_ge',

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
					'MAX(BankCode.name) as nome_banco',
					'MAX(BankCode.code) as codigo_banco',
					'MAX(CustomerUserBankAccount.account_type_id) as tipo_conta',
					'MAX(CustomerUserBankAccount.pix_type) as pix_type',
					'MAX(CustomerUserBankAccount.pix_id) as pix_id',
					'MAX(CustomerUserBankAccount.acc_number) as numero_conta',
					'MAX(CustomerUserBankAccount.acc_digit) as digito_conta',
					'MAX(CustomerUserBankAccount.branch_number) as numero_agencia',
					'MAX(CustomerUserBankAccount.branch_digit) as digito_agencia',

                    '(SELECT COUNT(1) 
                        FROM orders o
                            INNER JOIN order_items i ON i.order_id = o.id
                        WHERE i.customer_user_id = OrderItem.customer_user_id
                                AND o.id != Order.id
                    ) AS qtde_pedido',
				],
				'joins' => [
					[
						'table' => 'orders',
						'alias' => 'Order',
						'type' => 'INNER',
						'conditions' => ['Order.id = OrderItem.order_id']
					],
					[
						'table' => 'statuses',
						'alias' => 'OrderStatus',
						'type' => 'INNER',
						'conditions' => ['Order.status_id = OrderStatus.id']
					],
				
					[
						'table' => 'users',
						'alias' => 'UpdatedGe',
						'type' => 'LEFT',
						'conditions' => ['Order.user_updated_ge_id = UpdatedGe.id']
					],
					[
						'table' => 'economic_groups',
						'alias' => 'EconomicGroups',
						'type' => 'LEFT',
						'conditions' => ['Order.economic_group_id = EconomicGroups.id']
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
						'table' => 'benefit_types',
						'alias' => 'BenefitType',
						'type' => 'INNER',
						'conditions' => ['BenefitType.id = Benefit.benefit_type_id']
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
						'conditions' => ['CustomerUserAddress.customer_user_id = CustomerUser.id and CustomerUserAddress.address_type_id = 1']
					],
					[
						'table' => 'customer_user_addresses',
						'alias' => 'CustomerAddress',
						'type' => 'LEFT',
						'conditions' => ['CustomerAddress.customer_user_id = CustomerUser.id and CustomerAddress.address_type_id = 2']
					],
					[
						'table' => 'customer_user_bank_accounts',
						'alias' => 'CustomerUserBankAccount',
						'type' => 'LEFT',
						'conditions' => ['CustomerUserBankAccount.customer_user_id = CustomerUser.id']
					],
					[
						'table' => 'bank_codes',
						'alias' => 'BankCode',
						'type' => 'LEFT',
						'conditions' => ['BankCode.id = CustomerUserBankAccount.bank_code_id']
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
