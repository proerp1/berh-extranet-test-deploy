<?php
class FaqRelacionamento extends AppModel
{
    public $name = 'FaqRelacionamento';

    public $actsAs = ['Containable'];

    public $belongsTo = [
        'Faq' => [
            'className' => 'Faq',
            'foreignKey' => 'faq_id'
        ],
        'Supplier' => [
            'className' => 'Supplier',
            'foreignKey' => 'supplier_id'
        ]
    ];
}

