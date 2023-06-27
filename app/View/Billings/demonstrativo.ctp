<?php
    echo $this->element("abas_billings", ['id' => $id]);
?>
<div class="card mb-5 mb-xl-8">
    <div class="card-body py-7">
        <div class="table-responsive">
            <?php echo $this->element("Customers/demonstrativo_table"); ?>
        </div>

        <div class="col-sm-offset-2 col-sm-9">
            <a href="<?php echo $this->base.'/billings/mensalidade/'.$id; ?>" class="btn btn-light-dark">Voltar</a>
        </div>
    </div>
</div>