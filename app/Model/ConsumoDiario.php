<?php
App::uses('AppModel', 'Model');
class ConsumoDiario extends AppModel
{
    public $useTable = 'consumo_diario';
    public $name = 'ConsumoDiario';

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['ConsumoDiario.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public $actsAs = [
        'Upload.Upload' => [
            'arquivo'
        ]
    ];

    /*public $validate = [
        'arquivo' => [
            'isUnique' => [
                'rule' => 'isUnique',
                'message' => 'O arquivo fornecido já foi cadastrado'
            ]
        ]
    ];*/
}
