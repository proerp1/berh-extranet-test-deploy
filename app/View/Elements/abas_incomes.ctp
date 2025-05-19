<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/incomes/edit/'.$id; ?>">Dados</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['action'] == 'historico' ? 'active' : '' ?>" href="<?php echo $this->base.'/incomes/historico/'.$id; ?>">Histórico de cobrança</a>
	</li>
    <?php if ($this->request->data['Order']['id']) { ?>
        <li class="nav-item">
            <a class="nav-link <?php echo $this->request->params['action'] == 'nfse' ? 'active' : '' ?>" href="<?php echo $this->base.'/incomes/nfse/'.$id; ?>">Nota Fiscal de Serviço</a>
        </li>
    <?php } ?>
</ul>