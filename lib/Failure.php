<?php
namespace Smoothie;

/**
 * Represents a test failure.
 */
class Failure
{
	public $test_description;
	public $error_string;
	public $trace;

	public function __construct($test_description, $error_string, $trace)
	{
		$this->test_description = $test_description;
		$this->error_string = $error_string;
		$this->trace = $trace;
	}

	public function __toString()
	{
		$file = pathinfo($this->trace[0]['file']);
		$s = "$this->test_description ($file[filename])\n";

		foreach ($this->trace as $line)
			$s .= "  $line[file]($line[line]): $line[function]\n";

		if ($this->error_string)
		{
			$self = $this;
			$callback = function($match) use ($self)
			{
				return $self->trace[0]['args'][$match[1]];
			};

			$s .= "  " . preg_replace_callback('/{([0-9]+)}/',$callback,$this->error_string) . "\n";
		}
			
		return $s;
	}
}
?>