<?php 
App::uses('AuthComponent', 'Controller/Component');
class FaqFile extends AppModel {
	public $name = 'FaqFile';
	
	public $belongsTo = array(
		'File',
		'Status' => array(
			'className' => 'Status',
			'foreignKey' => 'status_id',
			'conditions' => array('Status.categoria' => 1)
		)
	);

	public function beforeFind($queryData) {
		$queryData['conditions'][] = array('FaqFile.data_cancel' => '1901-01-01 00:00:00');
		
		return $queryData;
	}

    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            $test_path = ROOT_SITE . 'app/webroot/files/faq/file/' . $results[$key][$this->name]['id'] . '/' . $results[$key][$this->name]['file'];

            $folder_id = $results[$key][$this->name]['id'];
            if (!file_exists($test_path)) {
                $folder_id = $results[$key][$this->name]['faq_id'];
            }
            $path = Configure::read('Extranet.link') . 'files/faq/file/' . $folder_id . '/' . $results[$key][$this->name]['file'];

            $results[$key][$this->name]['full_path'] = $path;
        }

        return $results;
    }

	public $actsAs = [
        'Upload.Upload' => [
            'file' => [
                'rootDir' => ROOT_SITE,
                'path' => '{ROOT}{DS}app{DS}webroot{DS}files{DS}faq{DS}{field}{DS}',
            ],
        ],
    ];
}
