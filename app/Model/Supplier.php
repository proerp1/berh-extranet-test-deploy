<?php
class Supplier extends AppModel
{
    public $name = 'Supplier';
    public $displayField = 'nome_fantasia';
    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1]
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Supplier.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public $validate = [
        'nome_fantasia' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'documento' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'O documento é obrigatório',
                'last' => false
            ],
            'isUnique' => [
                'rule' => 'isUnique',
                'message' => 'O documento fornecido já foi cadastrado.'
            ]
        ],
        'email' => [
            'email' => [
                'rule' => 'email',
                'message' => 'O e-mail deve ser válido',
                'last' => false
            ],
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'O e-mail é obrigatório',
                'last' => false
            ],
            'isUnique' => [
                'rule' => 'isUnique',
                'message' => 'O e-mail fornecido já foi cadastrado.'
            ]
        ],
        'cep' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'O cep é obrigatório'
            ],
        ],
        'endereco' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'O endereço é obrigatório'
            ],
        ],
        'numero' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'O numero é obrigatório',
                'last' => false
            ],
            'alphanumeric' => [
                'rule' => ['alphanumeric'],
                'message' => 'Somente números'
            ],
        ],
        'bairro' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'O bairro é obrigatório'
            ],
        ],
        'cidade' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'A cidade é obrigatória'
            ],
        ],
        'estado' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'O estado é obrigatório'
            ],
        ]
    ];
}
