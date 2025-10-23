<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'edit' ? 'active' : '' ?>" href="<?php echo $this->base; ?>/orders/edit/<?php echo $id; ?>">Pedido</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'boletos' ? 'active' : '' ?>" href="<?php echo $this->base; ?>/orders/boletos/<?php echo $id; ?>">Boletos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['controller'] == 'order_documents' && in_array($this->request->params['action'], ['documentos', 'documentos_add' , 'edit_documentos']) ? 'active' : '' ?>" href="<?php echo $this->base; ?>/order_documents/documentos/<?php echo $id; ?>">Comprovantes</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['controller'] == 'order_documents' && in_array($this->request->params['action'], ['index', 'add' , 'edit']) ? 'active' : '' ?>" href="<?php echo $this->base; ?>/order_documents/index/<?php echo $id; ?>">Notas fiscais</a>
    </li>

    <?php if (!in_array(CakeSession::read("Auth.User.Group.name"), ['Financeiro'])) { ?>
        <li class="nav-item">
            <a class="nav-link <?php echo $this->request->params['action'] == 'saldos' ? 'active' : '' ?>" href="<?php echo $this->base; ?>/orders/saldos/<?php echo $id; ?>">Movimentações</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($this->request->params['action'], ['operadoras', 'operadoras_detalhes']) ? 'active' : '' ?>" href="<?php echo $this->base; ?>/orders/operadoras/<?php echo $id; ?>">Operadoras</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $this->request->params['action'] == 'compras' ? 'active' : '' ?>" href="<?php echo $this->base; ?>/orders/compras/<?php echo $id; ?>">Compras</a>
        </li>
    <?php } ?>
    
    <?php /* ?>
    <li class="nav-item">
        <a class="nav-link <?php echo $this->request->params['action'] == 'descontos' ? 'active' : '' ?>" href="<?php echo $this->base; ?>/orders/descontos/<?php echo $id; ?>">Descontos</a>
    </li>
    <?php */ ?>
</ul>
