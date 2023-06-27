<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['controller'] == 'users' && $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/users/edit/'.$id; ?>">Dados</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['controller'] == 'user_resales' ? 'active' : '' ?>" href="<?php echo $this->base.'/user_resales/index/'.$id; ?>">Franquias</a>
    </li>
</ul>