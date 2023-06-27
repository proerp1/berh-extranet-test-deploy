<?php
class CnabLoteSicoob extends AppModel
{
    public $name = 'CnabLoteSicoob';
    public $useTable = 'cnab_lotes_sicoob';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 12]
        ],
        'Bank'
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CnabLoteSicoob.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }
}
