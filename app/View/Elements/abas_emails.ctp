<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/emails_campanhas/edit/'.$id; ?>">Dados</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'list_emails' ? 'active' : '' ?>" href="<?php echo $this->base.'/emails_campanhas/list_emails/'.$id; ?>">Selecionar Destinatários</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'view_emails' ? 'active' : '' ?>" href="<?php echo $this->base.'/emails_campanhas/view_emails/'.$id; ?>">Ver Destinatários</a>
	</li>
</ul>