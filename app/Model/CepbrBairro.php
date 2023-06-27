<?php 

App::uses('AuthComponent', 'Controller/Component');
class CepbrBairro extends AppModel {
	public $name = 'CepbrBairro';
	public $useTable = 'cepbr_bairro';
	public $primaryKey = 'id_bairro';

	public $hasMany = array(
    'CepbrEndereco' => array(
      'className' => 'CepbrEndereco',
      'foreignKey' => 'id_bairro'
    )
  );
}