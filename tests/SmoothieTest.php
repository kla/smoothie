<?php
namespace Smoothie;

require_once dirname(__FILE__) . '/../Smoothie.php';

context("a Smoothie instance", function() {
	context("filtering", function() {
		should("accept -n", function() {
			$smoothie = new Smoothie(array("-n","your regex"));
			assert_equals("/your regex/", $smoothie->filter);
		});

		should("accept --filter", function() {
			$smoothie = new Smoothie(array("--filter","your regex"));
			assert_equals("/your regex/", $smoothie->filter);
		});

		should("add slashes if missing", function() {
			$smoothie = new Smoothie(array("-n","your regex"));
			assert_equals("/your regex/", $smoothie->filter);
		});

		should("not add slahes if already there", function() {
			$smoothie = new Smoothie(array("-n","/your regex/"));
			assert_equals("/your regex/", $smoothie->filter);
		});

		should("return true if no filter was set", function() {
			$smoothie = new Smoothie();
			assert_true($smoothie->passes_filter("my awesome test"));
		});

		context("tests with passes_filter()", function() {
			setup(function() { transient()->smoothie = new Smoothie(array("-n","/my awesome test/")); }); 

			should("return true if filter was set and passes", function() {
				assert_true(transient()->smoothie->passes_filter("this is my awesome test that is awesome"));
			});
	
			should("return false if filter was set and does not pass", function() {
				assert_false(transient()->smoothie->passes_filter("blah"));
			});
		});
	});
});
?>