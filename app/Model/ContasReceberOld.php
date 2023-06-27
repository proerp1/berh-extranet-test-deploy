<?php 
App::uses('AuthComponent', 'Controller/Component');
class ContasReceberOld extends AppModel {
	public $useDbConfig = 'old_database';
  public $useTable = 'contas_receber';
}