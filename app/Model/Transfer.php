<?php

App::uses('AuthComponent', 'Controller/Component');
class Transfer extends AppModel
{
    public $name = 'Transfer';

    public $belongsTo = [
        'BankAccountOrigin' => [
            'className' => 'BankAccount',
            'foreignKey' => 'bank_account_origin_id',
        ],
        'BankAccountDest' => [
            'className' => 'BankAccount',
            'foreignKey' => 'bank_account_dest_id',
        ],
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 8],
        ],
        'UserCreated' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id',
        ],
    ];

    public $validate = [
        'bank_account_origin_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'bank_account_dest_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'value' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Transfer.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function beforeSave($options = [])
    {
        if (!empty($this->data[$this->alias]['value'])) {
            $this->data[$this->alias]['value'] = $this->priceFormatBeforeSave($this->data[$this->alias]['value']);
        }

        return true;
    }

    public function priceFormatBeforeSave($price)
    {
        $valueFormatado = str_replace('.', '', $price);

        return str_replace(',', '.', $valueFormatado);
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['value'])) {
                $results[$key][$this->alias]['value_nao_formatado'] = $results[$key][$this->alias]['value'];
                $results[$key][$this->alias]['value'] = number_format($results[$key][$this->alias]['value'], 2, ',', '.');
            }
        }

        return $results;
    }
}
