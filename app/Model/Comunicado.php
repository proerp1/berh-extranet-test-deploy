<?php
class Comunicado extends AppModel
{
    public $name = 'Comunicado';

   

    public $belongsTo = [
        'Status' => [
            'className' => 'Status',
            'foreignKey' => 'status_id',
            'conditions' => ['Status.categoria' => 1]
        ],
        'Categoria' => [
            'className' => 'Categoria',
            'foreignKey' => 'categoria_id',
            'fields' => ['id', 'name']
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Comunicado.data_cancel' => '1901-01-01 00:00:00'];
    
        return $queryData;
    }

    public function removeEmojis($string) {
        return preg_replace('/[\x{1F600}-\x{1F64F}'
            . '\x{1F300}-\x{1F5FF}'
            . '\x{1F680}-\x{1F6FF}'
            . '\x{2600}-\x{26FF}'
            . '\x{2700}-\x{27BF}'
            . ']+/u', '', $string);
    }

    public function beforeSave($options = []) {
        if (!empty($this->data[$this->alias]['file'])) {
            $file = $this->data[$this->alias]['file'];

            if (!empty($file['name'])) {
                $cleanName = $this->removeEmojis($file['name']);
                $cleanName = preg_replace('/[^A-Za-z0-9_\.\-]/', '_', $cleanName);
                $this->data[$this->alias]['file']['name'] = $cleanName;
            }
        }

        return parent::beforeSave($options);
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
