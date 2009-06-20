<?php
namespace Smoothie;
use Closure;
use stdClass;

class Smoothie
{
	public $assertions = 0;
	public $tests = 0;
	public $transient;
	public $filter;
	
	private $contexts = array();
	private $failures = array();
	private $errors = 0;
	private $failure;
	private $success;
	private $current_description;

	static $instance;

	public static function instance()
	{
		if (!static::$instance)
			static::$instance = new Smoothie($GLOBALS["argv"]);

		return static::$instance;
	}

	public function __construct($args=null)
	{
		for ($i=0,$n=count($args); $i<$n; ++$i)
		{
			if ($args[$i] == "-n" || $args[$i] == "--filter")
			{
				$this->filter = trim($args[++$i],"/");
				break;
			}
		}

		if ($this->filter)
			$this->filter = "/$this->filter/";
	}

	public function current_context()
	{
		return $this->contexts[count($this->contexts)-1];
	}

	public function display_fail_report()
	{
		echo "\n\n";

		if (!empty($this->failures))
		{
			$i = 1;

			foreach ($this->failures as $fail)
			{
				echo "$i) $fail\n";
				$i++;
			}
		}

		echo "$this->tests tests, $this->assertions assertions, " . count($this->failures) . " failures, $this->errors errors\n";
	}

	/**
	 * Perform setup for a test to be run.
	 */
	public function prepare()
	{
		$this->failure = null;
		$this->transient = null;
		$this->success = true;

		foreach ($this->contexts as $context)
		{
			if (($setup = $context->setup))
				$setup();
		}
	}

	/**
	 * Called after each test has been run.
	 */
	public function cleanup()
	{
		foreach ($this->contexts as $context)
		{
			if (($teardown = $context->teardown))
				$teardown();
		}
	}

	/**
	 * Returns true if the specified string passes the filter from the command line.
	 */
	public function passes_filter($description)
	{
		return !$this->filter || preg_match($this->filter,$description) ? true : false;
	}

	/**
	 * Runs a context.
	 *
	 * @param string $description Description of the context.
	 * @param Closure $closure The context code.
	 */
	public function run_context($description, Closure $closure)
	{
		array_push($this->contexts,new Context($description));
		$closure();
		array_pop($this->contexts);
	}

	/**
	 * Call this everytime an assertion is run.
	 */
	public function report_assertion()
	{
		$this->assertions++;
	}

	public function report_error()
	{
		echo "E";
		$this->success = false;
		$this->errors++;
		$this->failures[] = new Failure($this->test_description(), null, array_slice(debug_backtrace(),2));
	}

	/**
	 * Report an assertion failure for the current test.
	 *
	 * @param string $error_string Description of the error.
	 */
	public function report_failure($error_string)
	{
		echo "F";
		$this->success = false;
		$this->failures[] = $this->failure = new Failure($this->test_description(), $error_string, array_slice(debug_backtrace(),1));
	}

	/**
	 * Returns description for the current test.
	 */
	public function test_description()
	{
		$context_description = "";

		foreach ($this->contexts as $context)
			$context_description .= $context->description . ' ';

		return rtrim($context_description) . " should $this->current_description";
	}

	/**
	 * Runs a test.
	 *
	 * @param string $description Description of the test being run.
	 * @param Closure $closure The test to run.
	 */
	public function run_test($description, Closure $closure)
	{
		if (!$this->passes_filter($description))
			return;

		$this->tests++;
		$this->prepare();
		$this->current_description = $description;

		try {
			$closure();
		} catch (\Exception $e) {
			$this->report_error();
		}

		$this->cleanup();

		if ($this->success)
			echo ".";
	}
}

/**
 * The context class allows you to group together a set of closely related tests.
 * 
 * Each test (or should block) in the context will cause $setup to fire prior
 * and $teardown after the test has been run.
 * 
 * Contexts can be nested and setups/teardowns of the parent contexts will also run.
 */
class Context
{
	public $setup;
	public $teardown;
	public $description;

	public function __construct($description)
	{
		$this->description = $description;
		$this->transient = new stdClass();
	}
}
?>