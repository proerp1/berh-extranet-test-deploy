<?php
class CnabItemSicoob extends AppModel
{
    public $name = 'CnabItemSicoob';
    public $useTable = 'cnab_items_sicoob';

    public $belongsTo = [
        'CnabLoteSicoob' => [
            'foreignKey' => 'cnab_lote_sicoob_id',
        ],
        'Income',
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 13]
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CnabItemSicoob.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }
}
