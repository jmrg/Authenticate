<?php

use PHPUnit_Framework_TestCase as TestCase;
use Faker\Factory as Faker;

/**
 * Class BaseCase
 *
 * Base class for unit testing if you have some doubts please read the next sentences
 * With some random data
 * do some random process
 * then get a random output to assert
 *
 * @see https://laracasts.com/index/phpunit
 */
abstract class BaseCase extends TestCase
{
	/**
	 * Faker data library
	 *
	 * @var \Faker\Generator|null
	 */
	protected $faker = null;

	public function __construct($name = null, array $data = [], $dataName = ''){
        parent::__construct($name, $data, $dataName);
        $this->setFaker();
    }

    /**
	 * Gets the Faker library
	 *
	 * @return BaseCase
	 */
	public function setFaker(){
		!$this->faker && ($this->faker = Faker::create());

		return $this;
	}

	public static function setUpBeforeClass(){
	}

	public static function tearDownAfterClass(){
	}
}