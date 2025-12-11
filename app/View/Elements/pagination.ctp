<div class="row">
	<label class="col"><?php echo $this->Paginator->counter("{:count} registro(s)"); ?></label>
	<ul class="col pagination <?php echo !isset($limit) ? 'justify-content-end' : '' ?>">
		<?php 
			echo $this->Paginator->first('<i class="fa fa-angle-double-left"></i>', array('tag' => 'li', 'escape' => false, 'class' => 'page-item'), null, array('escape'=>false, 'class' => 'page-item disabled', 'tag' => 'li' ));
			if ($this->Paginator->hasPrev()) {
				echo $this->Paginator->prev('<i class="fa fa-angle-left"></i>', array('tag' => 'li', 'escape' => false, 'class' => 'page-item'), null, array('escape'=>false, 'class' => 'page-item disabled', 'tag' => 'li' ));
			}
			echo $this->Paginator->numbers( array('modulus' => 5, 'tag' => 'li', 'separator' => '', 'class' => 'page-item', 'currentClass' => 'active', 'currentTag' => 'a' ) );
			if ($this->Paginator->hasNext()) {
				echo $this->Paginator->next('<i class="fa fa-angle-right"></i>', array('tag' => 'li', 'escape' => false, 'class' => 'page-item'), null, array('escape'=>false, 'class' => 'page-item disabled', 'tag' => 'li' ));
			}
			echo $this->Paginator->last('<i class="fa fa-angle-double-right"></i>', array('tag' => 'li', 'escape' => false, 'class' => 'page-item'), null, array('escape'=>false, 'class' => 'page-item disabled', 'tag' => 'li' ));
		?> 
	</ul>

	<?php if (isset($limit)) { ?>
		<div class="col">
		    <?php
				echo $this->Form->create(false, ['type' => 'get', 'style' => 'display: flex;align-items: center;gap: 10px;', 'class' => 'justify-content-end']);
					$current_params = $this->request->query;

					foreach ($current_params as $key => $value) {
						if ($key !== 'limit' && $key !== 'url') {
							if (is_array($value)) {
								foreach ($value as $v) {
									echo $this->Form->hidden($key . '[]', ['value' => $v]);
								}
							} else {
								echo $this->Form->hidden($key, ['value' => $value]);
							}
						}
					}
					
					$pag_options = [5 => '5', 10 => '10', 20 => '20', 50 => '50'];

		            echo $this->Form->input('limit', [
		                'type' => 'select',
		                'options' => isset($limit_options) ? $limit_options : $pag_options,
		                'default' => $this->request->query('limit') ? $this->request->query('limit') : $limit,
		                'style' => 'flex: 0.1',
		                'div' => false,
		                'class' => 'form-select',
		                'label' => 'Linhas por página',
		                'onchange' => 'this.form.submit();'
		            ]);
		        echo $this->Form->end();
		    ?>
		</div>
	<?php } ?>
</div>

<style>
	.page-item:first-child a {
	    border-top-left-radius: 0.475rem;
	    border-bottom-left-radius: 0.475rem;
	}

	.page-item.active a {
	    z-index: 3;
	    color: #fff;
	    background-color: #009ef7;
	    border-color: transparent;
	}

	.page-item a {
	    display: flex;
	    justify-content: center;
	    align-items: center;
	    border-radius: 0.475rem;
	    height: 2.5rem;
	    min-width: 2.5rem;
	    font-weight: 500;
	    font-size: 1.075rem;
	    padding: 0.375rem 0.75rem;

	    position: relative;
        color: #5e6278;
        background-color: transparent;
        border: 0 solid transparent;
        transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
	}
</style>