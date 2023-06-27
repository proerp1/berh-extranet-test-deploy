<?php 
	$url = explode('/', $url);

?>

<ul class="nav nav-tabs">
	<li <?php echo $url[2] == 'billing_sales' ? 'class="active"' : '' ?>><a href="<?php echo $this->base.'/billing_sales/index'; ?>">Faturamentos</a></li>
	<li <?php echo $url[2] == 'faturamento_vendas' ? 'class="active"' : '' ?>><a href="<?php echo $this->base.'/billing_sales/faturamento_vendas'; ?>">Detalhes</a></li>
</ul>