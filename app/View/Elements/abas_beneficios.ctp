<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
<li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'benefits' && $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/benefits/edit/'.$id; ?>">Cliente</a>
	</li>
    <li class="nav-item">
	
    <li class="nav-item">
		<a class="nav-link <?php echo $this->request->params['controller'] == 'benefits' && $this->request->params['action'] == 'log_status' ? 'active' : '' ?>" href="<?php echo $this->base.'/benefits/log_status/'.$id; ?>">Log</a>
	</li>

    
</ul>