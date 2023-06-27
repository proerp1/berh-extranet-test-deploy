<?php echo $this->Html->script("html_editor/summernote", ['inline' => false]); ?>
<?php echo $this->Html->script("html_editor/summernote-pt-BR", ['inline' => false]); ?>

<?php echo $this->Html->css("html_editor/summernote", ['inline' => false]); ?>

<script type="text/javascript">
	$(document).ready(function(){

		$(".criar_campanha").on("click", function(){
			$("#summernote").val($("#summernote").code());

			$("form").submit();
		});

		//enviar email
		$("#send_list").on("click", function(){
			$("#summernote").val($("#summernote").code());

			$('#js-form-submit').attr('action', base_url+"/emails_campanhas/send_emails/<?php echo isset($id) ? $id : ''; ?>");

			$(this).prop('disabled', true);
			$(this).text('enviando...');

			$("form").submit();
		});

		<?php if (!isset($id)) { ?>
			//carregar html email
			$.ajax({
				type: 'post',
				url: base_url+"/emails_campanhas/template_email",
				//data: {id: id},
				//dataType: 'html',
				success: function(data){
					$('#summernote').code(data); 
				} 
			});
		<?php } ?>
		

		$('#summernote').summernote({
			lang: 'pt-BR',
			height: 500,
			toolbar : [
				['style', ['bold', 'italic', 'underline', 'clear']],
				['font', ['strikethrough', 'superscript', 'subscript']],
				['fontsize', ['fontsize', 'fontname']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']],
				['group', [ 'video', 'link', 'picture', 'hr' ]],
				['misc', [ 'codeview', 'undo', 'redo' ]],
				['help', [ 'help' ]],
			]
		});

		
		$("#show_mail_list").on("click", function(){
			if ($("#mail_list").is(":visible") == true) { 
			    $(this).html('<i class="fa fa-users"></i> Ver Destinatários');
			    $("#mail_list").fadeOut();
			}
			else {
			     $(this).html('<i class="fa fa-users"></i> Ocultar Destinatários');
			     $("#mail_list").fadeIn();
			}
		});

	});
</script>

<?php
    if (isset($id)) {
        echo $this->element("abas_emails", ['id' => $id]);
    }
?>

<div class="card mb-5 mb-xl-8">
    <div class="card-body pt-7 py-3">
        <?php echo $this->Form->create('EmailsCampanha', ["id" => "js-form-submit", "action" => $form_action, "method" => "post", 'inputDefaults' => ['div' => false, 'label' => false]]); ?>

        	<div class="mb-7">
                <div class="col-sm-offset-2 col-sm-9">
                    <a href="<?php echo $this->base.'/emails_campanhas' ?>" class="btn btn-light-dark">Voltar</a>
                    <button type="submit" class="btn btn-success js-salvar">Salvar</button>
                    <?php if (isset($id) && $send == false) { ?>
						<button type="button" id="send_list" class="btn btn-success">
							<i class="fa fa-share"></i>
							Enviar emails
						</button>
					<?php }?>
                </div>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">*Os campos exibidos entre as hashtags ("#") serão substituídos pelos dados do cliente ao enviar o email.</label>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Assunto</label>
                <?php echo $this->Form->input('subject', ["placeholder" => "Assunto", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

            <div class="mb-7 col">
                <label class="fw-semibold fs-6 mb-2">Corpo</label>
                <?php echo $this->Form->input('content', ["id" => "summernote", "class" => "form-control mb-3 mb-lg-0"]);  ?>
            </div>

        </form>
    </div>
</div>