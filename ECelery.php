<?php
/**
 * ECelery class file.
 *
 * @author Emanuele Cesena <ec@theneeds.com>
 * @version 0.1
 * @link https://github.com/ecesena/celery-yii
 * @copyright Copyright &copy; 2012 Emanuele Cesena
 * @license Beerware
 */
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <ec@theneeds.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Emanuele Cesena
 * ----------------------------------------------------------------------------
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

