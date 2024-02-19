<?php
	$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
?>
<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'resales' && $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/resales/edit/'.$id; ?>">Dados</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo (in_array($this->request->params['action'], ['users', 'edit_user', 'add_user']) and $tipo == 'revenda') ? 'active' : '' ?>" href="<?php echo $this->base.'/resales/users/'.$id.'?tipo=revenda'; ?>">Usuários parceiros</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['sellers', 'edit_seller', 'add_seller']) ? 'active' : '' ?>" href="<?php echo $this->base.'/resales/sellers/'.$id; ?>">Executivos</a>
	</li>
	<?php if (isset($seller_id)) { ?>
		<li class="nav-item">
			<a class="nav-link <?php echo (in_array($this->request->params['action'], ['users', 'edit_user', 'add_user']) and $tipo == 'vendedor') ? 'active' : '' ?>" href="<?php echo $this->base.'/resales/users/'.$seller_id.'/?tipo=vendedor'; ?>">Usuários do vendedor</a>
		</li>
	<?php } ?>
	<li class="nav-item">
		<a class="nav-link <?php echo ($this->request->params['action'] == 'carteira') ? 'active' : '' ?>" href="<?php echo $this->base.'/resales/carteira/'.$id; ?>">Carteira</a>
	</li>
</ul>