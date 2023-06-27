<?php 
App::uses('AuthComponent', 'Controller/Component');
class ProdutosNaoCadastrados extends AppModel {
	public $name = 'ProdutosNaoCadastrados';
	public $useTable = 'produtos_nao_cadastrados';

	public $belongsTo = array(
		'Billing'
	);
}