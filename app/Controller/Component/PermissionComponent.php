<?php
class PermissionComponent extends Component {
  public $components = array('Session');

  public function check($area, $permissao){
    $model_perm = ClassRegistry::init('Permission');

    $user_id = $this->Session->read("Auth.User.id");

    $permissao = $model_perm->is_permitted($user_id, $area, $permissao);
    
    if($permissao){
      return true;
    } else {
      return false;
    }
  }
}
?>
