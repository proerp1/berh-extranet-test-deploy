<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['controller'], ['economic_groups']) ? 'active' : '' ?>" href="<?php echo $this->base.'/economic_groups/edit/'.$id.'/'.$economicGroupId; ?>">Grupo econômico</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['controller'], ['economic_group_proposals']) ? 'active' : '' ?>" href="<?php echo $this->base.'/economic_group_proposals/index/'.$id.'/'.$economicGroupId; ?>">Propostas Grupo econômico</a>
	</li>
</ul>
