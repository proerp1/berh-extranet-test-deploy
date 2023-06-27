<?php 
App::uses('AuthComponent', 'Controller/Component');
class AnswerItem extends AppModel {
  public $name = 'AnswerItem';

  public $belongsTo = array(
    'Answer' => array(
      'className' => 'Answer',
      'foreignKey' => 'answer_id',
      'conditions' => array('Answer.data_cancel' => '1901-01-01 00:00:00')
    )
  );

  public function beforeFind($queryData) {

    $queryData['conditions'][] = array('AnswerItem.data_cancel' => '1901-01-01 00:00:00');
    
    return $queryData;
  }
}