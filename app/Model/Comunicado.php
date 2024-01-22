<?php
class Comunicado extends AppModel
{
    public $name = 'Comunicado';

   

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1]
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Comunicado.data_cancel' => '1901-01-01 00:00:00'];
    
        return $queryData;
    }

    public $validate = [
        'name' => [
            'required' => [
                'rule' => ['notBlank'],
                'message' => 'O nome é obrigatório'
            ]
        ]
    ];

    public $actsAs = array(
		'Upload.Upload' => array(
			'file'
		)
	);

    public function afterFind($results, $primary = false)
    {
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['data'])) {
                $results[$key][$this->alias]['data_nao_formatado'] = $results[$key][$this->alias]['data'];
                $results[$key][$this->alias]['data'] = date('d/m/Y', strtotime($results[$key][$this->alias]['data']));
            }
        }

        return $results;
    }
}
