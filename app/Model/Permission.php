<?php 
App::uses('AuthComponent', 'Controller/Component');
class Permission extends AppModel {
	public $name = 'Permission';

	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'order' => 'User.name DESC'
		)
	);

	public function get_permissions_by_group($id){
		$result = $this->query("SELECT pa.name, pa.id, pe.leitura, pe.escrita, pe.excluir FROM pages pa LEFT JOIN permissions pe on pa.id = pe.page_id and pe.group_id = ".$id." ORDER BY pa.name");
		return $result;
	}

	public function is_permitted($user_id, $area, $permission){
		$result = $this->query("SELECT *
															FROM users u
															INNER JOIN groups g ON g.id = u.group_id
															left join permissions p on p.group_id = g.id
															WHERE u.id = ".$user_id." and p.page_id = ".$area." and p.".$permission." = 1");

		return $result;
	}
}