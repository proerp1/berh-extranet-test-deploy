<?php 

App::uses('AuthComponent', 'Controller/Component');
class CepbrEstado extends AppModel {
	public $name = 'CepbrEstado';
	public $useTable = 'cepbr_estado';
	public $primaryKey = 'uf';

	public $hasMany = array(
    'CepbrCidade' => array(
      'className' => 'CepbrCidade',
      'foreignKey' => 'uf'
    )
  );
}