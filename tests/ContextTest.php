<?php
namespace Smoothie;

require_once dirname(__FILE__) . '/../Smoothie.php';

$GLOBALS['I_RAN_TEARDOWN'] = false;
$GLOBALS['I_RAN_FIRST_SIBLING_CONTEXT'] = false;

context("first context", function() {
	setup(function() { $GLOBALS['I_RAN_FIRST_SIBLING_CONTEXT'] = true; });
	should("be true", function() { assert_true(true); });
});

context("a context", function() {
	setup(function() { transient()->setup1 = true; });

	should("have ran the first context", function() {
		assert_true($GLOBALS['I_RAN_FIRST_SIBLING_CONTEXT']);
	});

	should("have called its setup", function() {
		assert_equals(true, transient()->setup1);
	});

	// TODO this is terrible
	should("count the number of tests", function() {
		assert_equals(4,Smoothie::instance()->tests);
	});

	// TODO this is terrible
	should("count the number of assertions", function() {
		assert_equals(4,Smoothie::instance()->assertions);
	});

	context("nested in a context", function() {
		setup(function() { transient()->setup2 = true; });
		teardown(function() { $GLOBALS['I_RAN_TEARDOWN'] = true; });

		should("have called its parent setup", function() {
			assert_equals(true, transient()->setup1);
		});

		should("have called its own setup", function() {
			assert_equals(true, transient()->setup2);
		});

		context("nested in a context", function() {
			setup(function() {
				transient()->setup3 = true;
			});

			should("have called all three setups", function() {
				assert_equals(true, transient()->setup1);
				assert_equals(true, transient()->setup2);
				assert_equals(true, transient()->setup3);
			});

			should("have the proper test description", function() {
				assert_equals("a context nested in a context nested in a context should have the proper test description",Smoothie::instance()->test_description());
			});
		});
	});

	should("have ran teardown", function() {
		assert_true($GLOBALS['I_RAN_TEARDOWN']);
	});

	context("with no setup", function() {
		should("work", function() {
			assert_true(true);
		});
	});

	// TODO
	should("cause a failure", function() {
		assert_equals(1, 2);
	});

	// TODO
	should("cause an error", function() {
		throw new \Exception("exception!");
	});
});
?>