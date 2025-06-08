<?php

class BancoPadrao extends AppModel
{
    public $useTable = 'banco_padrao';
    public $name = 'BancoPadrao';

    public $belongsTo = [
        'Bank' => [
            'className' => 'Bank',
            'foreignKey' => 'bank_id',
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['BancoPadrao.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
}
