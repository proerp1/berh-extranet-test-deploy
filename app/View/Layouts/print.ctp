<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'BeRH');
?>
<!DOCTYPE html>
<html class="no-js">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>
	</title>
	<meta http-equiv="content-language" content="pt">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<?php echo $this->element("favicon"); ?>
	
	<?php
        echo $this->Html->css('plugins.bundle');echo "\n\t";
        echo $this->Html->css('style.bundle');echo "\n\t";
		echo $this->Html->css('datepicker/bootstrap-datepicker3');echo "\n\t";

        echo $this->fetch('css');echo "\n\t";

        echo $this->Html->script('jquery.min');echo "\n\t";
        echo $this->Html->script('handlebars');echo "\n\t";
    ?>

    <style>
    	textarea.form-control.form-error {
    	    padding-right: calc(1.5em + 1.5rem);
    	    background-position: top calc(0.375em + 0.375rem) right calc(0.375em + 0.375rem);
    	}

    	.form-control.form-error {
    	    border-color: #f1416c !important;
    	    padding-right: calc(1.5em + 1.5rem);
    	    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23F1416C'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23F1416C' stroke='none'/%3e%3c/svg%3e");
    	    background-repeat: no-repeat;
    	    background-position: right calc(0.375em + 0.375rem) center;
    	    background-size: calc(0.75em + 0.75rem) calc(0.75em + 0.75rem);
    	}

    	.form-control[readonly] {
    		background-color: #f5f8fa;
    	}

    	.error-message {
    		width: 100%;
		    margin-top: 0.5rem;
		    font-size: .925rem;
		    color: #f1416c;
    	}
    </style>

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">

	<div class="d-flex flex-column flex-root">
		<div class="page d-flex flex-row flex-column-fluid">
			
			<div class="d-flex flex-column flex-row-fluid" id="kt_wrapper">
				<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

					<div class="post d-flex flex-column-fluid" id="kt_post">
						<div id="kt_content_container" class="container-fluid">
							<?php echo $this->fetch('content'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php
	echo $this->Html->script('plugins.bundle');echo "\n\t";
	echo $this->Html->script('scripts.bundle');echo "\n\t";
	echo $this->Html->script('datepicker/bootstrap-datepicker');echo "\n\t";
	echo $this->Html->script('datepicker/bootstrap-datepicker.pt-BR.min');echo "\n\t";

	echo $this->Html->script('bootstrapFileInput');echo "\n\t";
	echo $this->Html->script('jquery.maskedinput');echo "\n\t";
	echo $this->Html->script('jquery-maskmoney');echo "\n\t";
	echo $this->Html->script('modal');echo "\n\t";
	echo $this->Html->script('bootbox');echo "\n\t";

    echo $this->fetch('script');
?>

</body>
</html>
