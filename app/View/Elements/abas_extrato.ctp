<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['extrato']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/extrato/'.$id; ?>">Extrato</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['extrato_grupo_economico']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customers/extrato_grupo_economico/'.$id; ?>">Extrato por Grupo Economico</a>
	</li>
</ul>
