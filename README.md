# Smoothie #

Version 0.1 - June 19 2009

Smoothie is a testing library for PHP 5.3+. It's based on Shoulda and TestUnit from the ruby world.

## Example Usage ##

<?php
namespace Smoothie;

// use contexts to group related tests together
context("a context", function() {
	// setups are called prior to each test
	setup(function() {
		// use transient() to assign any data that should be re-used for each
		// test in a context
		transient()->name = "bob";
	});

	// called after each test finishes
	teardown(function() {});

	should("have a name", function() {
		assert_equals("bob", transient()->name);
	});

	should("add 1+1", function() {
		assert_equals(2, 1+1);
	});

	// contexts can be nested
	context("nested in a context", function() {
		// setups for parent contexts get called as well
		setup(function() {
			transient()->child = "child";
		});

		should("have called my parent context's setup", function() {
			assert_equals("bob", transient()->name);
		});

		should("call my setup", function() {
			assert_equals("child", transient()->child);
		});
	});
});
?>

Take a look in the tests directory for more examples.

# Running a Smoothie Test #

Use the smoothie command line script to run a smoothie test file:

; Run an individual test
$ smoothie MyTest.php

; Run all tests in a directory matching the filename pattern *Test.php
$ smoothie tests/unit

; Specify a regex filter with -n or --filter to only run certain tests
$ smoothie MyTest.php -n "/my awesome test/"
$ smoothie tests/unit --filter "/my awesome test/"
 