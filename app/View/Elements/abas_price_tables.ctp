<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/price_tables/edit/'.$id; ?>">Dados</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'products' ? 'active' : '' ?>" href="<?php echo $this->base.'/price_tables/products/'.$id; ?>">Produtos</a>
    </li>
</ul>