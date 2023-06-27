<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'customers' && $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/edit/'.$id; ?>">Dados</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['plans', 'edit_plan', 'add_plan']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/plans/'.$id; ?>">Planos</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['login_consulta', 'add_login_consulta', 'edit_login_consulta']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/login_consulta/'.$id; ?>">Logins de Consulta</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['mensalidade', 'historico', 'demonstrativo']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/mensalidade/'.$id; ?>">Faturas</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['users', 'add_user', 'edit_user']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/users/'.$id; ?>">Usuários</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['documents', 'add_document', 'edit_document']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/documents/'.$id; ?>">Documentos</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['negativacoes', 'add_negativacao', 'edit_negativacao']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/negativacoes/'.$id; ?>">Negativações</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'log_status' ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/log_status/'.$id; ?>">Log de status</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['negativacoes_cliente', 'add_negativacao_cliente', 'edit_negativacao_cliente']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/negativacoes_cliente/'.$id; ?>">Negativações para o cliente</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['descontos', 'add_desconto', 'edit_desconto']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/descontos/'.$id; ?>">Descontos</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo ($this->request->params['controller'] == 'customer_tokens') ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_tokens/index/'.$id; ?>">Tokens</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'index' && $this->request->params['controller'] == 'meproteja' ? 'active' : '' ?>" href="<?php echo $this->base.'/meproteja/index/'.$id; ?>">MeProteja</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo ($this->request->params['controller'] == 'acesso_strings') ? 'active' : '' ?>" href="<?php echo $this->base.'/acesso_strings/index/'.$id; ?>">Acesso strings</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo ($this->request->params['controller'] == 'customer_consumo_diario') ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_consumo_diario/index/'.$id; ?>">Consumo diário</a>
	</li>
</ul>