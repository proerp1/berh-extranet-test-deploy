<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/edit/'.$id; ?>">Dados</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'dashboard' ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/dashboard/'.$id; ?>">Dashboard</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['mensalidade', 'demonstrativo']) ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/mensalidade/'.$id; ?>">Mensalidade</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['produtos_nao_cadastrados']) ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/produtos_nao_cadastrados/'.$id; ?>">Produtos não cadastrados</a>
	</li>
</ul>