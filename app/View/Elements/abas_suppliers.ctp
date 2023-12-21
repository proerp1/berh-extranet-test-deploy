<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'suppliers' && $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/suppliers/edit/'.$id; ?>">Cliente</a>
	</li>
<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['documents', 'add_document', 'edit_document']) ? 'active' : '' ?>" href="<?php echo $this->base.'/suppliers/documents/'.$id; ?>">Documentos</a>
	</li>

</ul>