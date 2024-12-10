<?php

class CustomReportsComponent extends Component
{

    public function configPagination($type)
    {
        $paginate = [
            'default' => [
                'OrderItem' => [
                    'limit' => 100,
                    'order' => ['OrderItem.id' => 'desc'],
                    'fields' => [
                        'Customer.nome_primario',
                        'Customer.nome_secundario',
                        'Customer.documento',
                        'CustomerUser.name',
                        'CustomerUser.cpf',
                        'CustomerUser.tel',
                        'CustomerUser.cel',
                        'CustomerDepartment.name',
                        'CustomerUserItinerary.unit_price',
                        'CustomerUserItinerary.quantity',
                        'OrderItem.*',
                        'Benefit.code',
                        'Supplier.code',
                    ],
                    'joins' => [
                        [
                            'table' => 'orders',
                            'alias' => 'Order',
                            'type' => 'INNER',
                            'conditions' => ['Order.id = OrderItem.order_id'],
                        ],
                        [
                            'table' => 'customers',
                            'alias' => 'Customer',
                            'type' => 'INNER',
                            'conditions' => ['Customer.id = Order.customer_id'],
                        ],
                        [
                            'table' => 'customer_users',
                            'alias' => 'CustomerUser',
                            'type' => 'INNER',
                            'conditions' => ['CustomerUser.id = OrderItem.customer_user_id'],
                        ],
                        [
                            'table' => 'customer_departments',
                            'alias' => 'CustomerDepartment',
                            'type' => 'LEFT',
                            'conditions' => ['CustomerDepartment.id = CustomerUser.customer_departments_id'],
                        ],
                        [
                            'table' => 'cost_center',
                            'alias' => 'CostCenter',
                            'type' => 'LEFT',
                            'conditions' => ['CostCenter.id = CustomerUser.customer_departments_id'],
                        ],
                        [
                            'table' => 'customer_user_itineraries',
                            'alias' => 'CustomerUserItinerary',
                            'type' => 'INNER',
                            'conditions' => ['CustomerUserItinerary.id = OrderItem.customer_user_itinerary_id'],
                        ],
                        [
                            'table' => 'benefits',
                            'alias' => 'Benefit',
                            'type' => 'INNER',
                            'conditions' => ['Benefit.id = CustomerUserItinerary.benefit_id'],
                        ],
                        [
                            'table' => 'suppliers',
                            'alias' => 'Supplier',
                            'type' => 'INNER',
                            'conditions' => ['Supplier.id = Benefit.supplier_id'],
                        ],
                    ],
                    'recursive' => -1,
                ]
            ],
            'dados_bancarios' => [
                'OrderItem' => [
                    'limit' => 100,
                    'order' => ['OrderItem.id' => 'desc'],
                    'fields' => [
                        'Customer.nome_primario',
                        'Customer.nome_secundario',
                        'Customer.documento',
                        'CustomerUser.name',
                        'CustomerUser.cpf',
                        'CustomerUser.tel',
                        'CustomerUser.cel',
                        'CustomerUserBankAccount.*',
                        'BankCode.name',
                        'BankCode.code',
                        'BankAccountType.description'
                    ],
                    'joins' => [
                        [
                            'table' => 'orders',
                            'alias' => 'Order',
                            'type' => 'INNER',
                            'conditions' => ['Order.id = OrderItem.order_id'],
                        ],
                        [
                            'table' => 'customers',
                            'alias' => 'Customer',
                            'type' => 'INNER',
                            'conditions' => ['Customer.id = Order.customer_id'],
                        ],
                        [
                            'table' => 'customer_users',
                            'alias' => 'CustomerUser',
                            'type' => 'INNER',
                            'conditions' => ['CustomerUser.id = OrderItem.customer_user_id'],
                        ],
                        [
                            'table' => 'customer_user_bank_accounts',
                            'alias' => 'CustomerUserBankAccount',
                            'type' => 'LEFT',
                            'conditions' => ['CustomerUserBankAccount.customer_user_id = CustomerUser.id'],
                        ],
                        [
                            'table' => 'bank_codes',
                            'alias' => 'BankCode',
                            'type' => 'LEFT',
                            'conditions' => ['BankCode.id = CustomerUserBankAccount.bank_code_id'],
                        ],
                        [
                            'table' => 'bank_account_types',
                            'alias' => 'BankAccountType',
                            'type' => 'LEFT',
                            'conditions' => ['BankAccountType.id = CustomerUserBankAccount.account_type_id'],
                        ]
                    ],
                    'recursive' => -1,
                ]
            ],
            'residencia' => [
                'OrderItem' => [
                    'limit' => 100,
                    'order' => ['OrderItem.id' => 'desc'],
                    'fields' => [
                        'Customer.nome_primario',
                        'Customer.nome_secundario',
                        'Customer.documento',
                        'CustomerUser.name',
                        'CustomerUser.cpf',
                        'CustomerUser.tel',
                        'CustomerUser.cel',
                        'CustomerUserAddress.*'
                    ],
                    'joins' => [
                        [
                            'table' => 'orders',
                            'alias' => 'Order',
                            'type' => 'INNER',
                            'conditions' => ['Order.id = OrderItem.order_id'],
                        ],
                        [
                            'table' => 'customers',
                            'alias' => 'Customer',
                            'type' => 'INNER',
                            'conditions' => ['Customer.id = Order.customer_id'],
                        ],
                        [
                            'table' => 'customer_users',
                            'alias' => 'CustomerUser',
                            'type' => 'INNER',
                            'conditions' => ['CustomerUser.id = OrderItem.customer_user_id'],
                        ],
                        [
                            'table' => 'customer_user_addresses',
                            'alias' => 'CustomerUserAddress',
                            'type' => 'LEFT',
                            'conditions' => ['CustomerUserAddress.customer_user_id = CustomerUser.id AND CustomerUserAddress.address_type_id = 1'],
                        ]
                    ],
                    'recursive' => -1,
                ]
            ],
            'trabalho' => [
                'OrderItem' => [
                    'limit' => 100,
                    'order' => ['OrderItem.id' => 'desc'],
                    'fields' => [
                        'Customer.nome_primario',
                        'Customer.nome_secundario',
                        'Customer.documento',
                        'CustomerUser.name',
                        'CustomerUser.cpf',
                        'CustomerUser.tel',
                        'CustomerUser.cel',
                        'CustomerUserAddress.*'
                    ],
                    'joins' => [
                        [
                            'table' => 'orders',
                            'alias' => 'Order',
                            'type' => 'INNER',
                            'conditions' => ['Order.id = OrderItem.order_id'],
                        ],
                        [
                            'table' => 'customers',
                            'alias' => 'Customer',
                            'type' => 'INNER',
                            'conditions' => ['Customer.id = Order.customer_id'],
                        ],
                        [
                            'table' => 'customer_users',
                            'alias' => 'CustomerUser',
                            'type' => 'INNER',
                            'conditions' => ['CustomerUser.id = OrderItem.customer_user_id'],
                        ],
                        [
                            'table' => 'customer_user_addresses',
                            'alias' => 'CustomerUserAddress',
                            'type' => 'LEFT',
                            'conditions' => ['CustomerUserAddress.customer_user_id = CustomerUser.id AND CustomerUserAddress.address_type_id = 2'],
                        ]
                    ],
                    'recursive' => -1,
                ]
            ],
            'pedidos' => [
                'OrderItem' => [
                    'limit' => 100,
                    'order' => ['OrderItem.id' => 'desc'],
                    'fields' => [
                        'Customer.nome_primario',
                        'Customer.nome_secundario',
                        'Customer.documento',
                        'CustomerUser.name',
                        'CustomerUser.cpf',
                        'CustomerUser.matricula',
                        'CustomerUser.tel',
                        'CustomerUser.cel',
                        'CustomerDepartment.name',
                        'CustomerUserItinerary.unit_price',
                        'CustomerUserItinerary.quantity',
                        'OrderItem.*',
                        'Benefit.code',
                        'Benefit.name',
                        'Supplier.nome_fantasia',
                        'Supplier.id',
                        'Order.id',
                        'Order.created',
                        'Order.status_id',
                        'Status.name',
                        'Customer.codigo_associado',
                        '(SELECT COUNT(1) 
                            FROM orders o
                                INNER JOIN order_items i ON i.order_id = o.id
                            WHERE i.customer_user_id = OrderItem.customer_user_id
                                    AND o.id != Order.id
                        ) AS qtde_pedido',
                        'Order.primeiro_pedido',
                    ],
                    'joins' => [
                        [
                            'table' => 'orders',
                            'alias' => 'Order',
                            'type' => 'INNER',
                            'conditions' => ['Order.id = OrderItem.order_id'],
                        ],
                        [
                            'table' => 'statuses',
                            'alias' => 'Status',
                            'type' => 'INNER',
                            'conditions' => ['Order.status_id = Status.id'],
                        ],
                        [
                            'table' => 'customers',
                            'alias' => 'Customer',
                            'type' => 'INNER',
                            'conditions' => ['Customer.id = Order.customer_id'],
                        ],
                        [
                            'table' => 'customer_users',
                            'alias' => 'CustomerUser',
                            'type' => 'INNER',
                            'conditions' => ['CustomerUser.id = OrderItem.customer_user_id'],
                        ],
                        [
                            'table' => 'customer_departments',
                            'alias' => 'CustomerDepartment',
                            'type' => 'LEFT',
                            'conditions' => ['CustomerDepartment.id = CustomerUser.customer_departments_id'],
                        ],
                        [
                            'table' => 'cost_center',
                            'alias' => 'CostCenter',
                            'type' => 'LEFT',
                            'conditions' => ['CostCenter.id = CustomerUser.customer_departments_id'],
                        ],
                        [
                            'table' => 'customer_user_itineraries',
                            'alias' => 'CustomerUserItinerary',
                            'type' => 'INNER',
                            'conditions' => ['CustomerUserItinerary.id = OrderItem.customer_user_itinerary_id'],
                        ],
                        [
                            'table' => 'benefits',
                            'alias' => 'Benefit',
                            'type' => 'INNER',
                            'conditions' => ['Benefit.id = CustomerUserItinerary.benefit_id'],
                        ],
                        [
                            'table' => 'suppliers',
                            'alias' => 'Supplier',
                            'type' => 'INNER',
                            'conditions' => ['Supplier.id = Benefit.supplier_id'],
                        ],
                    ],
                    'recursive' => -1,
                ]
            ],
            'lgpd' => [
                'CustomerUser' => [
                    'limit' => 100,
                    'order' => ['CustomerUser.id' => 'desc'],
                    'fields' => [
                        'CustomerUser.id',
                        'CustomerUser.name',
                        'CustomerUser.cpf',
                        'CustomerUser.flag_lgpd',
                        'CustomerUser.data_flag_lgpd'
                    ],
                    'recursive' => -1,
                ]
            ],
        ];

        return $paginate[$type];
    }
}
