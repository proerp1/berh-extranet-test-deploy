<?php $elClass = isset($params['class']) ? $params['class'] : 'message error'; ?>
<div id="<?php echo h($key) ?>Message" class="<?php echo $elClass; ?>"><?php echo $message ?></div>
