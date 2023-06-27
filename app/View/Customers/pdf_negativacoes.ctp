<table>
	<tr>
		<td>
			<img src="<?php echo APP.'/webroot/img/logo.png' ?>" width="200">
		</td>
		<td style="vertical-align: top;font-size: 20px">
			<span>Razão Social: <?php echo $data[0]['Customer']['nome_primario'] ?></span>
			<br>
			<span>Data e hora: <?php echo date('d/m/Y H:i:s') ?></span>
		</td>
	</tr>
</table>

<br><br>
<?php echo $this->element("Customers/negativacoes_table"); ?>