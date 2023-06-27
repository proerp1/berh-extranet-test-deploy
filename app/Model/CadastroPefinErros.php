<?php 
App::uses('AuthComponent', 'Controller/Component');
class CadastroPefinErros extends AppModel {
  public $useTable = 'cadastro_pefin_erros';

  public $belongsTo = array(
		'CadastroPefin',
		'ErrosPefin'
	);
}