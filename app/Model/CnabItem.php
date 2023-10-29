<?php

App::uses('AuthComponent', 'Controller/Component');
class CnabItem extends AppModel
{
    public $name = 'CnabItem';

    public $belongsTo = [
        'CnabLote',
        'Income' => [
            'className' => 'Income',
            'foreignKey' => 'income_id',
            'conditions' => ['Income.data_cancel' => '1901-01-01 00:00:00'],
        ],
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 13],
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CnabItem.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
}
