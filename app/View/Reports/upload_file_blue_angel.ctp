<?php echo $this->Html->css('base_robos'); ?>

<div class="main-container d-flex align-items-center justify-content-center">
    <div class="card shadow-lg form-card border-0">
        <div class="card-body p-4 p-md-5">
            <h1 class="mb-4 text-center display-6 fw-bold">
                Enviar Arquivo
            </h1>
            
            <form action="<?php echo $this->Html->url(array( "controller" => "reports", "action" => "upload_file_blue_angel")); ?>" method="post" id="converterForm" enctype="multipart/form-data" class="row g-3">
                <div class="mb-3">
                    <label for="tipoConversor" class="form-label">Modalidade</label>
                    <select name="tipoConversor" id="tipoConversor" class="form-select select-form">
                        <option value="blue_angels">BLUE ANGELS</option>
                    </select>
                </div>

                <div id="buscaComplexa" class="col-12">
                    <label for="uploadExcel" class="form-label fw-semibold">
                        Upload do Excel
                    </label>
                    <input type="file" name="data[file]" accept=".xlsx, .xls, .xlsm" class="form-control" id="input3" title="Escolha o arquivo">
                </div>

                <div id="botaoConfirma" class="col-12 d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-custom fw-bold">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>