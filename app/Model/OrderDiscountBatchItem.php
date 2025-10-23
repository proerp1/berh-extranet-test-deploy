<?php
App::uses('AppModel', 'Model');

class OrderDiscountBatchItem extends AppModel {
    public $name = 'OrderDiscountBatchItem';

    public $belongsTo = [
        'OrderDiscountBatch' => [
            'className' => 'OrderDiscountBatch',
            'foreignKey' => 'batch_id'
        ],
        'Order' => [
            'className' => 'Order',
            'foreignKey' => 'order_parent_id'
        ]
    ];

    public $validate = [
        'batch_id' => [
            'notBlank' => [
                'rule' => 'notBlank',
                'message' => 'O lote é obrigatório'
            ],
            'numeric' => [
                'rule' => 'numeric',
                'message' => 'O ID do lote deve ser numérico'
            ]
        ],
        'order_parent_id' => [
            'notBlank' => [
                'rule' => 'notBlank',
                'message' => 'O pedido é obrigatório'
            ],
            'numeric' => [
                'rule' => 'numeric',
                'message' => 'O ID do pedido deve ser numérico'
            ]
        ]
    ];
}
