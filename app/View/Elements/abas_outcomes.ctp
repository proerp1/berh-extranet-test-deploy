<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'outcomes' && $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/outcomes/edit/'.$id; ?>">Cliente</a>
	</li>
<li class="nav-item">
		<a class="nav-link <?php echo in_array($this->request->params['action'], ['documents', 'add_document', 'edit_document']) ? 'active' : '' ?>" href="<?php echo $this->base.'/outcomes/documents/'.$id; ?>">Documentos</a>
	</li>

</ul>