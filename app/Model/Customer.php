<?php

class Customer extends AppModel
{
    public $name = 'Customer';
    public $displayField = 'nome_primario';
    public $actsAs = ['Containable'];

    public $belongsTo = [
        'Resale' => [
            'className' => 'Resale',
            'foreignKey' => 'cod_franquia',
        ],
        'Status',
        'Seller',
        'ActivityArea',
    ];

    public $hasOne = [
        'PlanoAtivo' => [
            'className' => 'PlanCustomer',
            'foreignKey' => 'customer_id',
            'conditions' => ['PlanoAtivo.status_id' => 1],
        ],
    ];

    public $validate = [
        'contato' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'cod_franquia' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'senha' => [
            'required' => [
                'rule' => ['maxLength', 6],
                'message' => 'Limite de 6 caracteres',
            ],
        ],
        'nome_primario' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'documento' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O documento é obrigatório',
                'last' => false,
            ],
            'isUnique' => [
                'rule' => 'isUnique',
                'message' => 'O documento fornecido já foi cadastrado',
                'on' => 'create',
            ],
        ],
        'email' => [
            'email' => [
                'rule' => 'email',
                'message' => 'O e-mail deve ser válido',
                'last' => false,
            ],
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O e-mail é obrigatório',
                'last' => false,
            ],
            'isUnique' => [
                'rule' => 'isUnique',
                'message' => 'O e-mail fornecido já foi cadastrado',
            ],
        ],
        'cep' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O cep é obrigatório',
            ],
        ],
        'endereco' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O endereço é obrigatório',
            ],
        ],
        'numero' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O numero é obrigatório',
                'last' => false,
            ],
            'alphanumeric' => [
                'rule' => ['alphanumeric'],
                'message' => 'Somente números',
            ],
        ],
        'bairro' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O bairro é obrigatório',
            ],
        ],
        'cidade' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'A cidade é obrigatória',
            ],
        ],
        'estado' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O estado é obrigatório',
            ],
        ],
        'telefone1' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O telefone é obrigatório',
            ],
        ],
        'responsavel' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'cod_vendedor' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'activity_area_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'faturar' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'desconto' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'enviar_email' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
    ];

    public function dateFormatBeforeSave($dateString)
    {
        return date('Y-m-d', strtotime($this->date_converter($dateString)));
    }

    public function date_converter($_date = null)
    {
        $format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
        if ($_date != null && preg_match($format, $_date, $partes)) {
            return $partes[3].'-'.$partes[2].'-'.$partes[1];
        }

        return false;
    }

    public function priceFormatBeforeSave($price)
    {
        $valueFormatado = str_replace('.', '', $price);

        return str_replace(',', '.', $valueFormatado);
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created_nao_formatado'] = $results[$key][$this->alias]['created'];
                $results[$key][$this->alias]['created'] = date('d/m/Y', strtotime($results[$key][$this->alias]['created']));
            }
        }

        return $results;
    }

    public function beforeSave($options = [])
    {
        if (!empty($this->data[$this->alias]['cnpj'])) {
            $this->data[$this->alias]['cnpj'] = preg_replace('/\D/', '', $this->data[$this->alias]['cnpj']);
        }

        return true;
    }

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Customer.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function is_unique($check)
    {
        $count = $this->find('count', ['conditions' => [$check, 'Customer.status_id != 5'], 'recursive' => -1]);

        if ($count > 0) {
            return false;
        }

        return true;
    }
}
