<?php
	require 'vendor/autoload.php';

	use \Mockery;

	$m = Mockery::mock('service');
	var_dump($m);
