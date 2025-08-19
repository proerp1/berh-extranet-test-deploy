<?php

class CustomerBenefitCode extends AppModel
{
    public $belongsTo = [
        'Customer',
        'UserCreated' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id',
        ],
        'Benefit'
    ];

    public $validate = [
        'benefit_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O benefício BE é obrigatório',
                'last' => false,
            ],
            'uniqueCombo' => array(
                'rule' => array('checkUnique', array('benefit_id', 'customer_id')),
                'message' => 'Esse código BE já está vinculado à outro código Cliente.'
            )
        ],
        'code_customer' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O código cliente é obrigatório',
                'last' => false,
            ],
        ],
    ];

    public function checkUnique($data, $fields) {
        if (!is_array($fields)) {
          $fields = array($fields);
        }

        $conditions = array();
        foreach ($fields as $field) {
          $conditions[$field] = $this->data[$this->alias][$field];
        }

        if (!empty($this->id)) {
          $conditions[$this->alias . '.id !='] = $this->id;
        }

        return $this->isUnique($conditions, false);
    }

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CustomerBenefitCode.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
}
