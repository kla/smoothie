<?php
namespace Smoothie;
use Closure;

function context($description, Closure $closure)
{
	Smoothie::instance()->run_context($description,$closure);
}

/**
 * Returns the transient object which you can use to store data in setup(). This
 * object is re-created each time a test is run.
 */
function transient()
{
	return Smoothie::instance()->transient;
}

function setup(Closure $closure)
{
	Smoothie::instance()->current_context()->setup = $closure;
}

function teardown(Closure $closure)
{
	Smoothie::instance()->current_context()->teardown = $closure;
}

function should($description, Closure $closure)
{
	Smoothie::instance()->run_test($description,$closure);
}

function should_eventually($description)
{
}

function assertion()
{
	Smoothie::instance()->report_assertion();
}

function assert_equals($expected, $actual)
{
	assertion();
	if ($expected != $actual) Smoothie::instance()->report_failure("<$expected> expected but was\n  <$actual>");
}

function assert_true($actual)
{
	assertion();
	if (!$actual) Smoothie::instance()->report_failure("<$actual> exected to be true");
}

function assert_false($actual)
{
	assertion();
	if ($actual) Smoothie::instance()->report_failure("<$actual> expected to be false");
}
?>