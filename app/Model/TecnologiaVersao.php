<?php
class TecnologiaVersao extends AppModel
{
    public $name = 'TecnologiaVersao';
    public $useTable = 'tecnologia_versoes';

    public $belongsTo = [
        'Tecnologia',
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['TecnologiaVersao.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public $validate = [
        'name' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório'
            ]
        ]
    ];
}
