<?php 
App::uses('AuthComponent', 'Controller/Component');
class LinhasNaoImportadas extends AppModel {
	public $name = 'LinhasNaoImportadas';
	public $useTable = 'linhas_nao_importadas';

	public $belongsTo = array(
		'Product'
	);
}