<?php 
App::uses('AuthComponent', 'Controller/Component');
class ItemOption extends AppModel {
  public $name = 'ItemOption';

  public function beforeFind($queryData) {

    $queryData['conditions'][] = array('ItemOption.data_cancel' => '1901-01-01 00:00:00');
    
    return $queryData;
  }
}