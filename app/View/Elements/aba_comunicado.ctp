<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'comunicados' && $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/comunicados/edit/'.$id; ?>">Comunicado</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'comunicados' && $this->request->params['action'] == 'clientes' ? 'active' : '' ?>" href="<?php echo $this->base.'/comunicados/clientes/'.$id; ?>">Clientes</a>
	</li>
</ul>
