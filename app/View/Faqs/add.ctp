<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('Faq', [
            'url' => ['controller' => 'faqs', 'action' => $form_action],
            'id' => 'js-form-submit',
            'method' => 'post',
            'inputDefaults' => ['div' => false, 'label' => false],
            'enctype' => 'multipart/form-data'
        ]); ?>
        <!-- Sistema Destino -->
        <div class="row">
            <div class="mb-7 col-md-12">
                <label for="sistema_destino" class="fw-semibold fs-6 mb-2 required">Onde exibir esta FAQ?</label>
                <?php echo $this->Form->input('sistema_destino', [
                    'type' => 'select',
                    'options' => [
                        'todos' => 'SIG e Cliente',
                        'sig' => 'Apenas SIG (Extranet)',
                        'cliente' => 'Apenas Área do Cliente'
                    ],
                    'class' => 'form-select form-select-solid',
                    'label' => false,
                    'required' => true
                ]); ?>
            </div>
        </div>

        <!-- Linha para Categoria -->
        <div class="row">
            <div class="mb-7 col-md-12">
                <label for="categoria_faq_id" class="fw-semibold fs-6 mb-2 required">Categoria</label>
                <?php
                    echo $this->Form->input('categoria_faq_id', [
                        'type' => 'select',
                        'options' => $categoriasFaq,
                        'empty' => 'Selecione uma categoria',
                        'class' => 'form-select form-select-solid',
                        'label' => false,
                        'required' => true
                    ]);
                ?>
            </div>
        </div>

<!-- Linha para Atribuir a um ou mais Fornecedores -->
<div class="row">
    <div class="mb-7 col-md-12">
        <label for="supplier_id" class="fw-semibold fs-6 mb-2">Atribuir a Fornecedores</label>
        <?php
            $fornecedores = [0 => 'Todos os fornecedores'] + $fornecedores;

          echo $this->Form->input('FaqRelacionamento.supplier_id', [
    'type' => 'select',
    'multiple' => true,
    'options' => $fornecedores,
    'label' => false,
    'class' => 'form-select form-select-solid',
    'id' => 'select-suppliers'
]);

        ?>
    </div>
</div>


        <!-- Linha para Pergunta -->
        <div class="row">
            <div class="mb-7 col-md-12">
                <label for="pergunta" class="fw-semibold fs-6 mb-2 required">Título da Pergunta</label>
                <?php echo $this->Form->input('pergunta', [
                    'id' => 'pergunta',
                    'placeholder' => 'Digite o título da pergunta',
                    'class' => 'form-control form-control-solid auto-expand',
                    'type' => 'textarea',
                    'rows' => 1,
                    'required' => true,
                    'style' => 'overflow:hidden; resize:none;'
                ]); ?>
            </div>
        </div>

        <!-- Linha para Resposta -->
        <div class="row">
            <div class="mb-7 col-md-12">
                <label for="resposta" class="fw-semibold fs-6 mb-2 required">Resposta</label>
                <?php echo $this->Form->input('resposta', [
                    'id' => 'resposta',
                    'placeholder' => 'Digite a resposta completa',
                    'class' => 'form-control form-control-solid auto-expand',
                    'type' => 'textarea',
                    'rows' => 1,
                    'required' => true,
                    'style' => 'overflow:hidden; resize:none;'
                ]); ?>
            </div>
        </div>

        <!-- Upload de Documentação -->
        <h2>Documentos</h2>
        <div id="files">
            <?php if (isset($this->request->data['FaqFile'])) { ?>
                <?php for ($i = 0; $i < count($this->request->data['FaqFile']); $i++) { ?>
                    <?php $faqFile = $this->request->data['FaqFile'][$i]; ?>
                    <div class="row mb-7">
                        <div class="col-md-1">
                            <a class="btn btn-sm btn-primary" download href="<?php echo $faqFile['full_path']; ?>">
                                <i class="fa fa-download"></i>
                            </a>
                            <div class="btn btn-sm btn-danger remove-file"><i class="fa fa-trash"></i></div>
                        </div>
                        <div class="col-md-11">
                            <input type="hidden" name="data[keep_file_ids][]" value="<?= $faqFile['id'] ?>">
                            <h2><?= $faqFile['file'] ?></h2>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col-12">
                <button type="button" id="add-file" class="btn btn-primary">Novo Arquivo</button>
            </div>
        </div>


        <!-- Botões -->
        <div class="d-flex justify-content-end">
            <a href="<?php echo $this->base.'/faqs'; ?>" class="btn btn-light-dark me-3">Voltar</a>
            <button type="submit" class="btn btn-success js-salvar">Salvar</button>
        </div>

        <?php echo $this->Form->end(); ?>
    </div>
</div>

<!-- Script para expansão automática dos textareas -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const textareas = document.querySelectorAll('textarea.auto-expand');

    textareas.forEach(function (textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';

        textarea.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#select-suppliers').select2({
        placeholder: "Selecione os fornecedores",
        width: '100%'
    });

    const supplierSelect = document.getElementById('select-suppliers');

    function handleSupplierSelection() {
        const selected = Array.from(supplierSelect.selectedOptions).map(opt => opt.value);
        const isAllSelected = selected.includes("0");

        for (const option of supplierSelect.options) {
            if (option.value !== "0") {
                option.disabled = isAllSelected;
            }
        }
    }

    supplierSelect.addEventListener('change', handleSupplierSelection);
    handleSupplierSelection();

    $("#add-file").on('click', function () {
        $("#files").append(
            '<div class="row mb-7">' +
                '<div class="col-md-1">' +
                    '<div class="btn btn-sm btn-danger remove-file">' +
                        '<i class="fa fa-trash"></i>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-11">' +
                    '<input type="file" class="form-control form-control-solid" name="data[FaqFile][file][]">' +
                '</div>' +
            '</div>'
        )
    })
});

$(document).on('ready', function () {
    $('.remove-file').click(function () {
        $(this).parent().parent().remove()
    })
})
</script>