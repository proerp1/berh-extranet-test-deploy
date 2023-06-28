<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/billing_sales/edit/'.$id; ?>">Dados</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'revenda' ? 'active' : '' ?>" href="<?php echo $this->base.'/billing_sales/revenda/'.$id; ?>">Revendas</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'berh' ? 'active' : '' ?>" href="<?php echo $this->base.'/billing_sales/berh/'.$id; ?>">BeRH</a>
	</li>
</ul>