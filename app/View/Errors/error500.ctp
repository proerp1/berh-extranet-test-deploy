<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>

<?php
  echo $this->Html->css('font-awesome/font-awesome.min');echo "\n\t";
  echo '<!--[if IE 7]><link rel="stylesheet" href="font-awesome/font-awesome-ie7.min.css"><![endif]-->';echo "\n\n\t";
  echo $this->Html->css('error');echo "\n\n\t";
?>
<?php $this->layout = 'ajax'; ?>
<div class="page-err" style="background-color: #373a50">
    <div class="text-center">
        <div class="err-status">
            <h1 style="color: #222537">500</h1>
        </div>
        <div class="err-message" style="background-color: #222537">
            <h2>Algo de errado aconteceu.</h2>
        </div>
        <div class="err-body">
          <a href="<?php echo $this->base.'/' ?>" class="btn btn-lg btn-goback">
              Voltar para a dashboard
          </a>
        </div>
    </div>
</div>
<?php
  echo $name.'<br>';
  echo __d('cake', 'Error').'<br>';
  echo __d('cake', 'An Internal Error Has Occurred.').'<br>';
  if (Configure::read('debug') > 0 ):
    echo $this->element('exception_stack_trace');
  endif;
?>
