<?php

App::uses('AuthComponent', 'Controller/Component');
class Atendimento extends AppModel
{
    public $name = 'Atendimento';
    public $useTable = 'atendimento';

    public $belongsTo = [
        'Customer',
        'Department',
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 9],
        ],
    ];

    public $actsAs = [
        'Upload.Upload' => [
            'file_atendimento' => [
                'rootDir' => ROOT_SITE,
                'path' => '{ROOT}{DS}app{DS}webroot{DS}files{DS}{model}{DS}{field}{DS}',
            ],
        ],
    ];

    public $validate = [
        'subject' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'text' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'department_id' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
        'customer_id' => [
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
        'message' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'Campo obrigatório',
            ],
        ],
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Atendimento.data_cancel' => '1901-01-01 00:00:00'];

        return $queryData;
    }
    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val['Atendimento']['data_atendimento'])) {
                $results[$key]['Atendimento']['data_atendimento'] = date('d/m/Y H:i', strtotime($val['Atendimento']['data_atendimento']));
            }
            if (isset($val['Atendimento']['data_finalizacao'])) {
                $results[$key]['Atendimento']['data_finalizacao'] = date('d/m/Y H:i', strtotime($val['Atendimento']['data_finalizacao']));
            }
        }
    
        return $results;
    }
    

    public function beforeSave($options = [])
    {
        if (!empty($this->data['Atendimento']['data_atendimento'])) {
            $this->data['Atendimento']['data_atendimento'] = $this->dateTimeFormatBeforeSave($this->data['Atendimento']['data_atendimento']);
        }

        return true;
    }

    public function dateTimeFormatBeforeSave($dateString)
    {
        $date = explode(' ', $dateString);

        $date[0] = date('Y-m-d', strtotime(str_replace('/', '-', $date[0])));

        return $date[0].' '.$date[1];
    }
}
