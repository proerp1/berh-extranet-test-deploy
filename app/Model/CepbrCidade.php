<?php 

App::uses('AuthComponent', 'Controller/Component');
class CepbrCidade extends AppModel {
	public $name = 'CepbrCidade';
	public $useTable = 'cepbr_cidade';
	public $primaryKey = 'id_cidade';

	public $hasMany = array(
    'CepbrEndereco' => array(
      'className' => 'CepbrEndereco',
      'foreignKey' => 'id_cidade'
    )
  );

  public $belongsTo = array(
    'CepbrEstado' => array(
      'className' => 'CepbrEstado',
      'foreignKey' => 'uf'
    )
  );
}