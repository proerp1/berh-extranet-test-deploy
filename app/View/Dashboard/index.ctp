<style>
    .faq-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .faq-item {
        border-bottom: 1px solid #ddd;
        padding-bottom: 1rem;
    }

    .faq-question {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-size: 1.3rem;
        font-weight: bold;
        color: #333;
        transition: color 0.3s;
    }

    .faq-question:hover,
    .faq-question.active {
        color: #ED0677;
    }

    .icon {
        font-size: 1.5rem;
        transition: transform 0.3s ease;
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.6s ease, opacity 0.4s ease;
    }

    .faq-answer.open {
        max-height: 2000px;
        opacity: 1;
        margin-top: 0.5rem;
    }

    .faq-answer p {
        margin: 0;
        color: #555;
        font-size: 1rem;
        line-height: 1.6;
    }

    .badge-fornecedor {
        display: inline-block;
        padding: 0.3rem 0.6rem;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 20px;
        margin: 0.2rem 0.2rem 0.6rem 0;
        background-color: #f1f1f1;
        color: #333;
        border: 1px solid #ccc;
        transition: 0.3s;
    }

    .badge-fornecedor:nth-child(odd) {
        background-color: #3b3b64;
        color: white;
    }

    .badge-fornecedor:nth-child(even) {
        background-color: #3b3b64 ;
        color: #f1f1f1;
    }
</style>

<div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
    <a href="<?php echo $this->base; ?>/dashboard/compras">Compras</a>
    <a href="<?php echo $this->base; ?>/dashboard/comercial">Comercial</a>
    <a href="<?php echo $this->base; ?>/financeiro_report">Financeiro</a>
    <a href="<?php echo $this->base; ?>/dashboard/cliente">Cliente</a>
    <a href="<?php echo $this->base; ?>/dashboard/outros">Outros</a>
    <a href="<?php echo $this->base; ?>/dashboard/expedicao">Expedição</a>
    <a href="<?php echo $this->base; ?>/dashboard/fornecedores">Fornecedores</a>
    <a href="<?php echo $this->base; ?>/dashboard/oportunidade">Oportunidades</a>
    <a href="<?php echo $this->base; ?>/dashboard/orcamentos">Orçamentos</a>
    <a href="<?php echo $this->base; ?>/dashboard/produto">Produto</a>
    <a href="<?php echo $this->base; ?>/dashboard/resumo">Resumo</a>
</div>

<?php foreach ($categorias as $categoria): ?>
    <div style="margin-top: 3rem;">
        <h2 style="font-size: 1.5rem; color: #000;"><?php echo h($categoria['CategoriaFaq']['nome']); ?></h2>
        <div class="faq-list">
            <?php foreach ($categoria['Faqs'] as $index => $faq): 
                $uid = $categoria['CategoriaFaq']['id'] . '-' . $index;
                $file = $faq['Faq']['file'] ?? null;
                $faqId = $faq['Faq']['id'] ?? null;
            ?>
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq('<?php echo $uid; ?>')">
                        <span><?php echo h($faq['Faq']['pergunta']); ?></span>
                        <span id="icon-<?php echo $uid; ?>" class="icon">+</span>
                    </div>
                    <div id="faq-<?php echo $uid; ?>" class="faq-answer">

                        <?php if (!empty($faq['FaqRelacionamento'])): ?>
                            <p style="margin: 0 0 0.3rem; color: #000; font-size: 0.95rem; font-weight: 500;">
                                Essa FAQ se aplica para os seguintes fornecedores:
                            </p>
                            <div style="margin-bottom: 10px; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <?php foreach ($faq['FaqRelacionamento'] as $rel): ?>
                                    <?php if ($rel['supplier_id'] == 0): ?>
                                        <span class="badge-fornecedor">Todos os fornecedores</span>
                                    <?php elseif (!empty($rel['Supplier']['nome_fantasia'])): ?>
                                        <span class="badge-fornecedor"><?php echo h($rel['Supplier']['nome_fantasia']); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <p><?php echo nl2br(h($faq['Faq']['resposta'])); ?></p>

                        <?php if (!empty($file)): ?>
                            <div style="margin-top: 0.8rem;">
                                <a 
                                    href="<?php echo $this->webroot . 'files/faq/file/' . $faqId . '/' . $file; ?>" 
                                    download="<?php echo h($file); ?>" 
                                    style="color: #ED0677; font-weight: 500; text-decoration: none;"
                                >
                                    📎 Baixar Anexo: <?php echo h($file); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<script>
    let lastOpen = null;

    function toggleFaq(index) {
        const current = document.getElementById("faq-" + index);
        const currentIcon = document.getElementById("icon-" + index);
        const currentQuestion = currentIcon.parentElement;

        if (lastOpen === index) {
            current.classList.remove("open");
            currentIcon.innerText = "+";
            currentQuestion.classList.remove("active");
            lastOpen = null;
            return;
        }

        if (lastOpen !== null) {
            const prev = document.getElementById("faq-" + lastOpen);
            const prevIcon = document.getElementById("icon-" + lastOpen);
            const prevQuestion = prevIcon.parentElement;

            if (prev) prev.classList.remove("open");
            if (prevIcon) prevIcon.innerText = "+";
            if (prevQuestion) prevQuestion.classList.remove("active");
        }

        current.classList.add("open");
        currentIcon.innerText = "−";
        currentQuestion.classList.add("active");
        lastOpen = index;
    }

    document.addEventListener('click', function(event) {
        const isClickInside = event.target.closest('.faq-question');
        if (!isClickInside && lastOpen !== null) {
            const prev = document.getElementById("faq-" + lastOpen);
            const prevIcon = document.getElementById("icon-" + lastOpen);
            const prevQuestion = prevIcon.parentElement;

            if (prev) prev.classList.remove("open");
            if (prevIcon) prevIcon.innerText = "+";
            if (prevQuestion) prevQuestion.classList.remove("active");
            lastOpen = null;
        }
    });
</script>
