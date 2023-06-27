<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base.'/products/edit/'.$id; ?>">Dados</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['controller'] == 'product_attributes' ? 'active' : '' ?>" href="<?php echo $this->base.'/product_attributes/index/'.$id; ?>">Atributos</a>
    </li>
    <?php if ($tipo == 2) { ?>
        <li class="nav-item">
            <a class="nav-link <?php echo $this->request->params['action'] == 'features' ? 'active' : '' ?>" href="<?php echo $this->base.'/products/features/'.$id; ?>">Features</a>
        </li>
    <?php } ?>
    <?php if ($tipo == 4) { ?>
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($this->request->params['action'], ['features_string', 'edit_feature_string']) ? 'active' : '' ?>" href="<?php echo $this->base.'/products/features_string/'.$id; ?>">Features</a>
        </li>
    <?php } ?>
</ul>