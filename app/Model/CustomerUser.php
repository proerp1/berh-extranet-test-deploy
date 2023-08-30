<?php

App::uses('AuthComponent', 'Controller/Component');
class CustomerUser extends AppModel
{
    public $name = 'CustomerUser';
    public $actsAs = ['Containable'];

    public $belongsTo = [
        'Customer' => [
            // 'conditions' => array('CustomerUser.resale' => 0, 'CustomerUser.seller' => 0)
        ],
        'Resale' => [
            'className' => 'Resale',
            'foreignKey' => 'customer_id',
            // 'conditions' => array('CustomerUser.resale' => 1)
        ],
        'Seller' => [
            'className' => 'Seller',
            'foreignKey' => 'customer_id',
            // 'conditions' => array('CustomerUser.seller' => 1)
        ],
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1],
        ],
        'CustomerDepartment' => [
            'className' => 'CustomerDepartment',
            'foreignKey' => 'customer_departments_id',
        ],
        'CostCenter' => [
            'className' => 'CostCenter',
            'foreignKey' => 'customer_cost_center_id',
        ],
    ];

    public $hasAndBelongsToMany = [
        'EconomicGroup' => [
            'className' => 'EconomicGroup',
            'joinTable' => 'customer_users_economic_groups',
            'foreignKey' => 'customer_user_id',
            'associationForeignKey' => 'economic_group_id',
        ],
    ];

    public $validate = [
        'email' => [
            'email' => [
                'rule' => 'email',
                'message' => 'O e-mail deve ser válido',
            ],
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
            'isUnique' => [
                'rule' => 'isUnique',
                'message' => 'O e-mail fornecido já foi cadastrado',
            ],
        ],
        'password' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatória',
            ],
        ],
        'name' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'status_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CustomerUser.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['data_nascimento'])) {
                $results[$key][$this->alias]['data_nascimento_nao_formatado'] = $results[$key][$this->alias]['data_nascimento'];
                $results[$key][$this->alias]['data_nascimento'] = date('d/m/Y', strtotime($results[$key][$this->alias]['data_nascimento']));
            }

            if (isset($val[$this->alias]['cel'])) {
                $cel = str_replace(['(', ')', ' ', '-'], '', $results[$key][$this->alias]['cel']);
                $results[$key][$this->alias]['cel_sem_ddd'] = substr($cel, 2);
                $results[$key][$this->alias]['ddd_cel'] = substr($cel, 0, 2);
            } else {
                $results[$key][$this->alias]['cel_sem_ddd'] = null;
                $results[$key][$this->alias]['ddd_cel'] = null;
            }

            if (isset($val[$this->alias]['tel'])) {
                $tel = str_replace(['(', ')', ' ', '-'], '', $results[$key][$this->alias]['tel']);
                $results[$key][$this->alias]['tel_sem_ddd'] = substr($tel, 2);
                $results[$key][$this->alias]['ddd_tel'] = substr($tel, 0, 2);
            } else {
                $results[$key][$this->alias]['tel_sem_ddd'] = null;
                $results[$key][$this->alias]['ddd_tel'] = null;
            }
        }

        return $results;
    }

    public function beforeSave($options = [])
    {
        if (isset($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password']);
        }

        if (!empty($this->data[$this->alias]['data_nascimento'])) {
            $this->data[$this->alias]['data_nascimento'] = $this->dateFormatBeforeSave($this->data[$this->alias]['data_nascimento']);
        }

        return true;
    }

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
}
