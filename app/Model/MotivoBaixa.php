<?php 
App::uses('AuthComponent', 'Controller/Component');
class MotivoBaixa extends AppModel {
	public $name = 'MotivoBaixa';
	public $useTable = 'motivo_baixa';
	public $displayField = 'nome';
}