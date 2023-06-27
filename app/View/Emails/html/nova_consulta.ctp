<table cellpadding="0" cellspacing="0" border="0">
	<tbody style="font-size:14px; font-family:Georgia,&quot;Times New Roman&quot;,Times,serif; color:#777777; font-style:italic; line-height:18px;">
		<tr>
			<td style="font-size:14px;font-family:Georgia,&quot;Times New Roman&quot;,Times,serif;color:#777777;font-style:italic;line-height:18px;padding-top:2px">
				Olá <?php echo $nome ?>,<br><br>
			</td>
		</tr>

		<tr>
			<td style="font-size:14px;font-family:Georgia,&quot;Times New Roman&quot;,Times,serif;color:#777777;font-style:italic;line-height:18px;padding-top:2px">
			Bons amigos não têm segredos. Por isso, avisamos que uma empresa consultou seu <?php echo $tipo_documento; ?> na base de dados da Serasa Experian.<br>
			<br>Acesse o seu relatório no ambiente logado para ver mais detalhes.<br>
			<br><br>Até mais,<br>
			</td>
		</tr>
		<tr>
			<td style="font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;font-size:12px;padding-top:4px"><br>Use o link abaixo para acessar o nosso sistema: &nbsp; <br><a href="<?php echo $link ?>" style="border:none;color:#0084b4;text-decoration:none" target="_blank"><?php echo $link ?></a> </td>
		</tr>
		<tr>
			<td style="min-height:13px;font-size:13px;line-height:13px">&nbsp;</td>
		</tr>
		<tr>
			<td>
			</td>
		</tr>
	</tbody>
</table>