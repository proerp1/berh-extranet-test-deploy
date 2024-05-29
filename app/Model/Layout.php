<?php 
App::uses('AuthComponent', 'Controller/Component');
class Layout extends AppModel {
  public $name = 'Layout';

  public function beforeFind($queryData) {

    $queryData['conditions'][] = array('Layout.data_cancel' => '1901-01-01 00:00:00');
    
    return $queryData;
  }
}