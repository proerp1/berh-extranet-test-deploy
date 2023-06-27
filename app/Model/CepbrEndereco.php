<?php 

App::uses('AuthComponent', 'Controller/Component');
class CepbrEndereco extends AppModel {
	public $name = 'CepbrEndereco';
	public $useTable = 'cepbr_endereco';
	public $primaryKey = 'cep';

	public $belongsTo = array(
    'CepbrCidade' => array(
      'className' => 'CepbrCidade',
      'foreignKey' => 'id_cidade'
    ),
    'CepbrBairro' => array(
      'className' => 'CepbrBairro',
      'foreignKey' => 'id_bairro'
    )
  );
}