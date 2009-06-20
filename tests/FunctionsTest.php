<?php
namespace Smoothie;

require_once dirname(__FILE__) . '/../Smoothie.php';

context("assertions", function() {
	context("with assert_equals", function() {
		setup(function() { assert_equals("bob", "bob!"); });
		should_eventually("report failure if expected does not match actual value");
	});
});
?>