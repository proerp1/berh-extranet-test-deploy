<?php

class EmailConfig {

	
	
	public $default = array(
		'transport' => 'Smtp',
		'from' => ['no-reply@berh.com.br' => 'BeRH'],
        'host' => 'smtplw.com.br',
        'port' => 587,
        'username' => 'credch11',
        'password' => 'eZxzLchO4347',
		'charset' => 'utf-8'
	);

	public $fatura = array(
		'transport' => 'Smtp',
		'from' => ['financeiro@berh.com.br' => 'BeRH'],
        'host' => 'smtplw.com.br',
        'port' => 587,
        'username' => 'credch11',
        'password' => 'eZxzLchO4347',
		'charset' => 'utf-8'
	);

	/*
	public $default = array(
		'transport' => 'Smtp',
		'from' => ['no-reply@berh.com.br' => 'BeRH'],
        'host' => 'mail.berh.com.br',
        'port' => 587,
        'username' => 'no-reply@berh.com.br',
        'password' => 'berh@1',
		'charset' => 'utf-8'
	);

	public $fatura = array(
		'transport' => 'Smtp',
		'from' => ['no-reply@berh.com.br' => 'BeRH'],
        'host' => 'mail.berh.com.br',
        'port' => 587,
        'username' => 'no-reply@berh.com.br',
        'password' => 'berh@1',
		'charset' => 'utf-8'
	);
	

	
	*/
}
