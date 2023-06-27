<?php
class Plan extends AppModel
{
    public $name = 'Plan';
    public $displayField = 'description';

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1]
        ]
    ];

    public $hasMany = [
        'PlanProduct',
        'PlanCustomer'
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Plan.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function beforeSave($options = [])
    {
        if (!empty($this->data['Plan']['value'])) {
            $this->data['Plan']['value'] = $this->priceFormatBeforeSave($this->data['Plan']['value']);
        }
        if (!empty($this->data['Plan']['commission'])) {
            $this->data['Plan']['commission'] = $this->priceFormatBeforeSave($this->data['Plan']['commission']);
        }
        
        return true;
    }

    public function priceFormatBeforeSave($price)
    {
        $valueFormatado = str_replace('.', '', $price);
        $valueFormatado = str_replace(',', '.', $valueFormatado);

        return $valueFormatado;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val['Plan']['value'])) {
                $results[$key]['Plan']['value_nao_formatado'] = $results[$key]['Plan']['value'];
                $results[$key]['Plan']['value'] = number_format($results[$key]['Plan']['value'], 2, ',', '.');
            }
            if (isset($val['Plan']['commission'])) {
                $results[$key]['Plan']['commission'] = number_format($results[$key]['Plan']['commission'], 2, ',', '.');
            }
        }

        return $results;
    }

    public $validate = [
        'description' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'value' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ],
        'commission' => [
            'required' => [
                'rule' => ['notEmpty'],
                'message' => 'Campo obrigatório'
            ]
        ]
    ];
}
