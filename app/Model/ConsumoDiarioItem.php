<?php
App::uses('AppModel', 'Model');
class ConsumoDiarioItem extends AppModel
{
    public $useTable = 'consumo_diario_itens';
    public $name = 'ConsumoDiarioItem';

    public $belongsTo = [
        'ConsumoDiario',
        'Customer',
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['ConsumoDiarioItem.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    /*public $validate = [
        'arquivo' => [
            'isUnique' => [
                'rule' => 'isUnique',
                'message' => 'O arquivo fornecido já foi cadastrado'
            ]
        ]
    ];*/
}
