<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/edit/'.$id; ?>">Dados</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'dashboard' ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/dashboard/'.$id; ?>">Dashboard</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['mensalidade', 'demonstrativo']) ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/mensalidade/'.$id; ?>">1º - Mensalidade</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['negativacao']) ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/negativacao/'.$id; ?>">2º - SERASA</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['pefin']) ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/pefin/'.$id; ?>">3º - PEFIN</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['credcheck']) ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/credcheck/'.$id; ?>">4º - BeRH/String</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['produtos_nao_cadastrados']) ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/produtos_nao_cadastrados/'.$id; ?>">Produtos não cadastrados</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['linhas_nao_importadas']) ? 'active' : '' ?>" href="<?php echo $this->base.'/billings/linhas_nao_importadas/'.$id; ?>">Linhas não importadas</a>
	</li>
</ul>