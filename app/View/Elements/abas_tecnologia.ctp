<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'tecnologias' ? 'active' : '' ?>" href="<?php echo $this->base.'/tecnologias/edit/'.$id; ?>">Tecnologia</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'tecnologia_versao' ? 'active' : '' ?>" href="<?php echo $this->base.'/tecnologia_versao/index/'.$id; ?>">Versões</a>
	</li>
</ul>
