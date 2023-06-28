<?php

class EmailConfig {

	
	
	public $default = array(
		'transport' => 'Smtp',
		'from' => ['no-reply@credcheck.com.br' => 'BeRH'],
        'host' => 'smtplw.com.br',
        'port' => 587,
        'username' => 'credch11',
        'password' => 'eZxzLchO4347',
		'charset' => 'utf-8'
	);

	public $fatura = array(
		'transport' => 'Smtp',
		'from' => ['financeiro@credcheck.com.br' => 'BeRH'],
        'host' => 'smtplw.com.br',
        'port' => 587,
        'username' => 'credch11',
        'password' => 'eZxzLchO4347',
		'charset' => 'utf-8'
	);

	/*
	public $default = array(
		'transport' => 'Smtp',
		'from' => ['no-reply@credcheck.com.br' => 'BeRH'],
        'host' => 'mail.credcheck.com.br',
        'port' => 587,
        'username' => 'no-reply@credcheck.com.br',
        'password' => 'credcheck@1',
		'charset' => 'utf-8'
	);

	public $fatura = array(
		'transport' => 'Smtp',
		'from' => ['no-reply@credcheck.com.br' => 'BeRH'],
        'host' => 'mail.credcheck.com.br',
        'port' => 587,
        'username' => 'no-reply@credcheck.com.br',
        'password' => 'credcheck@1',
		'charset' => 'utf-8'
	);
	

	
	*/
}
