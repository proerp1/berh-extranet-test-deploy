<?php
/**
 * Application level
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $components = array(
		'Session',
		'Auth' => array(
			'loginRedirect' => array('controller' => 'dashboard', 'action' => 'index'),
			'logoutRedirect' => array('controller' => 'users', 'action' => 'login'),
			'authenticate' => array('Form' => array( 'scope' => array('User.status_id' => 1)))
		),
		'Flash', 'Permission'
	);

	public $uses = ['Atendimento', 'CustomerFile'];

	public function beforeFilter() 
	{
		$_SERVER['QUERY_STRING'] = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
		if ($this->Auth->user('id')) {
			$pendentes = $this->Atendimento->find('count', ['conditions' => ['Atendimento.status_id' => 34]]);
			
			if (!$this->Permission->check(80, "leitura")) {
				$pendente_arquivo = $this->CustomerFile->find('count', ['conditions' => ['CustomerFile.status_id' => 100, 'Customer.cod_franquia' => CakeSession::read('Auth.User.resales'), 'Customer.seller_id' => CakeSession::read('Auth.User.id')]]);
			} else {
				$pendente_arquivo = $this->CustomerFile->find('count', ['conditions' => ['CustomerFile.status_id' => 100, 'Customer.cod_franquia' => CakeSession::read('Auth.User.resales')]]);
			}

			$this->set(compact('pendentes', 'pendente_arquivo'));
		}
	}
}
