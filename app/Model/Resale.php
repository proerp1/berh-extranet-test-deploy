<?php
class Resale extends AppModel
{
    public $name = 'Resale';
    public $displayField = 'nome_fantasia';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1]
        ],
        'Vencimento',
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Resale.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val['Resale']['valor_recebido_cliente'])) {
                $results[$key]['Resale']['valor_recebido_cliente_nao_formatado'] = $results[$key]['Resale']['valor_recebido_cliente'];
                $results[$key]['Resale']['valor_recebido_cliente'] = number_format($results[$key]['Resale']['valor_recebido_cliente'], 2, '.', '');
            }
        }

        return $results;
    }

    public $validate = [
        'bank_account_id' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'vencimento_id' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'valor_recebido_cliente' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'principal_fonte' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'territorial_coverage_id' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'nome_fantasia' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'razao_social' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'cnpj' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'O CNPJ é obrigatório',
                'last' => false
            ],
            'isUnique' => [
                'rule' => 'isUnique',
                'message' => 'O CNPJ fornecido já foi cadastrado.'
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
