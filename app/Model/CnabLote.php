<?php

class CnabLote extends AppModel
{
    public $name = 'CnabLote';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 12],
        ],
        'Bank',
        'UserCreated' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id',
        ],
    ];

    public $hasMany = [
        'CnabItem'
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CnabLote.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
}
