<?php $action = $this->request->params['action']; ?>

<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
	<li class="nav-item">
		<a class="nav-link <?php echo (!isset($_GET['t']) and $action == 'index') ? 'active' : '' ?>" href="<?php echo $this->base.'/outcomes'; ?>">Todos</a>
	</li>

	<?php foreach ($status as $dados){ ?>
		<li class="nav-item">
			<a class="nav-link <?php echo (isset($_GET['t']) and $_GET['t'] == $dados['Status']['id']) ? 'active' : '' ?>" href="<?php echo $this->base.'/outcomes/?t='.$dados['Status']['id']; ?>"><?php echo $dados['Status']['name'] ?></a>
		</li>
	<?php } ?>

	<li class="nav-item">
		<a class="nav-link <?php echo (!isset($_GET['t']) and $action == 'all_documents') ? 'active' : '' ?>" href="<?php echo $this->base.'/outcomes/all_documents'; ?>">Documentos</a>
	</li>
</ul>