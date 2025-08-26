<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'suppliers' && $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/suppliers/edit/'.$id; ?>">Dados</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['documents', 'add_document', 'edit_document']) ? 'active' : '' ?>" href="<?php echo $this->base.'/suppliers/documents/'.$id; ?>">Documentos</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['controller'], ['customer_supplier_logins']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_supplier_logins/index/2/'.$id; ?>">Logins e Senhas</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['volume_tiers', 'add_volume_tier', 'edit_volume_tier']) ? 'active' : '' ?>" href="<?php echo $this->base.'/suppliers/volume_tiers/'.$id; ?>">Faixas de Volume</a>
	</li>
    <li class="nav-item">
        <a class="nav-link <?php echo in_array($this->request->params['controller'], ['log_supplier']) ? 'active' : '' ?>" href="<?php echo $this->base.'/log_supplier/index/'.$id; ?>">Histórico Alterações</a>
    </li>
</ul>