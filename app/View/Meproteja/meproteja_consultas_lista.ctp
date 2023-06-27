<div class="card mb-5 mb-xl-8">
    <div class="card-body">
        <h4>Última atualização: <?php echo $dt_ultimo_arquivo; ?></h4>
        <h4>Importar Arquivos</h4>
        <form action="<?php echo $this->base;?>/meproteja/upload_manual" method="post" enctype="multipart/form-data">
            <input type="file" class="btn-primary" name="imp_consulta[]" multiple title="Escolha o arquivo">

            <input type="submit" value="Enviar" class="btn btn-success">
        </form>
    </div>
</div>