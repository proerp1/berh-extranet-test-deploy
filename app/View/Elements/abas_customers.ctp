<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'customers' && $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/edit/'.$id; ?>">Cliente</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['controller'], ['proposals']) ? 'active' : '' ?>" href="<?php echo $this->base.'/proposals/index/'.$id; ?>">Proposta</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['boletos']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/boletos/'.$id; ?>">Boletos</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'customer_users' && in_array($this->request->params['action'], ['index_users', 'add_user', 'edit_user']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_users/index_users/'.$id; ?>">Usuários</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'customer_users' && in_array($this->request->params['action'], ['index', 'add', 'edit']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_users/index/'.$id; ?>">Beneficiários</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['documents', 'add_document', 'edit_document']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/documents/'.$id; ?>">Documentos</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'log_status' ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/log_status/'.$id; ?>">Log de status</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'log_ge' ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/log_ge/'.$id; ?>">Log GE</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'params_ge' ? 'active' : '' ?>" href="<?php echo $this->base.'/params_ge/index/'.$id; ?>">Parâmetros GE</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['controller'], ['economic_groups']) ? 'active' : '' ?>" href="<?php echo $this->base.'/economic_groups/index/'.$id; ?>">Grupos econômicos</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['files', 'add_file', 'edit_file']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/files/'.$id; ?>">Arquivos</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['extrato', 'extrato_grupo_economico']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/extrato/'.$id; ?>">Extrato</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['controller'], ['customer_supplier_logins']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_supplier_logins/index/1/'.$id; ?>">Logins e Senhas</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['controller'], ['customer_address']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_address/index/'.$id; ?>">Endereços</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['controller'], ['customers_benefit_codes']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_benefit_codes/index/'.$id; ?>">De/Para Benefício</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['controller'], ['log_customer']) ? 'active' : '' ?>" href="<?php echo $this->base.'/log_customer/index/'.$id; ?>">Histórico Alterações</a>
	</li>
</ul>
