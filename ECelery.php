<?php
/**
 * ECelery class file.
 *
 * @author Emanuele Cesena <ec@theneeds.com>
 * @version 0.2
 * @link https://github.com/ecesena/celery-yii
 * @copyright Copyright &copy; 2012 Emanuele Cesena
 * @license BSD 2-clause license
 */
/*
 * Copyright (c) 2012, Emanuele Cesena <ec@theneeds.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are those
 * of the authors and should not be interpreted as representing official policies,
 * either expressed or implied, of the FreeBSD Project.
 */
require('vendors/celery-php/celery.php');

/**
 * celery-yii is a YII extension to dispatch Celery (http://celeryproject.org) tasks.
 *
 * It is a wrapper to celery-php (https://github.com/gjedeer/celery-php),
 * released under the same Beerware license.
 *
 * @author Emanuele Cesena <ec@theneeds.com>
 * @since 1.1.9
 */
class ECelery extends CApplicationComponent
{
	public $host = 'localhost';
	public $port = 5672;

	public $login = null;
	public $password = null;
	public $vhost = null;

	public $exchange = 'celery';
	public $binding = 'celery';

	private $_celery = null;

	/**
	 * Initialize ECelery
	 * @throws CeleryException if AMQP is not installed or an error occured
	 */
	public function init()
	{
		parent::init();
		self::initCelery();
	}

	/**
	 * Wrapper to Celery constructor
	 * @throws CeleryException
	 */
	public function initCelery()
	{
		Yii::trace('Initializing celery-yii', 'ECelery.init');
		$this->_celery = new Celery(
			$this->host,
			$this->login,
			$this->password,
			$this->vhost,
			$this->exchange,
			$this->binding,
			$this->port
		);
		if ($this->_celery===null)
			throw new CeleryException("Unable to initialize celery-php");
	}

	/**
	 * Post a task to Celery
	 * @param string $task Name of the task, prefixed with module name (like tasks.add for function add() in task.py)
	 * @param array $args Array of arguments (kwargs call when $args is associative)
	 */
	public function postTask($task, $args=array())
	{
		if ($this->_celery===null)
			self::initCelery();

		Yii::trace("Submitting task: $task", 'ECelery.postTask');
		return $this->_celery->PostTask($task, $args);
	}
}

