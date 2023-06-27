<?php
class ItensResposta extends AppModel
{
    public $name = 'ItensResposta';
    public $useTable = 'itensResposta';
    public $primaryKey = 'itemRespostaID';

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['ItensResposta.itemRespostaDataCancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public $belongsTo = [
        'Resposta' => [
            'className' => 'Resposta',
            'foreignKey' => 'respostaID',
            'conditions' => ['Resposta.respostaDataCancel' => '1901-01-01 00:00:00']
        ]
    ];
}
