<?php echo $this->element("abas_customers", ['id' => $customer_id]); ?>
<div class="card mb-5 mb-xl-8">
    <div class="card-body py-7">
        <div class="table-responsive">
            <?php echo $this->element("Customers/demonstrativo_table"); ?>
        </div>

        <div class="col-sm-offset-2 col-sm-9">
            <a href="<?php echo $this->base.'/customers/mensalidade/'.$customer_id; ?>" class="btn btn-light-dark">Voltar</a>
            <a href="<?php echo $this->base.'/customers/pdf_demonstrativo/'.$id.'/'.$customer_id.'/'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="<?php echo $this->base.'/customers/demonstrativo/'.$id.'/'.$customer_id.'/?excel&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''); ?>" class="btn btn-primary">
                <i class="fas fa-file-excel"></i> Excel
            </a>
        </div>
    </div>
</div>