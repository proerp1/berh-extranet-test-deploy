<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6 aba_cst_usr">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'customer_users' && $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_users/edit/'.$id.'/'.$user_id; ?>">Beneficiário</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['addresses', 'edit_address', 'add_address']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_users/addresses/'.$id.'/'.$user_id; ?>">Endereços</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['bank_info', 'add_bank_info', 'edit_bank_info']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_users/bank_info/'.$id.'/'.$user_id; ?>">Dados Bancários</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['itineraries', 'add_itineraries', 'edit_itineraries']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_users/itineraries/'.$id.'/'.$user_id; ?>">Benefícios</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['vacations', 'add_vacation', 'edit_vacation']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_users/vacations/'.$id.'/'.$user_id; ?>">Férias</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['transactions']) ? 'active' : '' ?>" href="<?php echo $this->base.'/customer_users/transactions/'.$id.'/'.$user_id; ?>">Transação</a>
	</li>
</ul>

<style>
	.aba_cst_usr a {
		color: #61646e !important;
	}
</style>
