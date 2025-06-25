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
