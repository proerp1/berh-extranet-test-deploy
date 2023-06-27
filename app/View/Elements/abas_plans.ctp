<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/plans/edit/'.$id; ?>">Dados</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'composition' && $_GET['composicao'] == 1 ? 'active' : '' ?>" href="<?php echo $this->base.'/plans/composition/'.$id.'?composicao=1'; ?>">Composição SERASA</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'composition' && $_GET['composicao'] == 2 ? 'active' : '' ?>" href="<?php echo $this->base.'/plans/composition/'.$id.'?composicao=2'; ?>">Composição BeRH</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'composition' && $_GET['composicao'] == 4 ? 'active' : '' ?>" href="<?php echo $this->base.'/plans/composition/'.$id.'?composicao=4'; ?>">Composição String SERASA</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'customers' ? 'active' : '' ?>" href="<?php echo $this->base.'/plans/customers/'.$id; ?>">Clientes</a>
    </li>
</ul>