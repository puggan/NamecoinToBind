<?php

	$startTime = microtime(true);
	if (PHP_SAPI != 'cli') {
		ob_start();
	}

	require __DIR__.'/config.php';

	$rows = explode("\n", file_get_contents($bindZonesList.'db.namecoin.bit'));

	$arguments = array();
	$arguments[] = "named-checkzone";
	$arguments[] = "bit";
	$arguments[] = escapeshellarg($bindZonesList.'db.namecoin.bit');
	$warnings = shell_exec(implode(" ", $arguments));

	print_r($warnings);

	preg_match_all("#db.namecoin.bit:([0-9]+):(.*)#", $warnings, $m);
	$row_errors = array_combine($m[1], $m[2]);

	$removed = 0;

	foreach($row_errors as $row_nr => $error)
	{
		echo PHP_EOL;
		echo "row: " . $row_nr . PHP_EOL;
		echo "error: ". $error . PHP_EOL;
		echo "content: " . $rows[$row_nr - 1] . PHP_EOL;

		unset($rows[$row_nr - 1]);

		$removed++;
	}

	if($removed)
	{
		echo PHP_EOL;
		echo "Removed {$removed} invalid lines from zonefile {$bindZonesList}db.namecoin.bit" . PHP_EOL;
		file_put_contents($bindZonesList.'db.namecoin.bit', implode("\n", $rows));
	}
	else
	{
		echo "No lines removed, all ok" . PHP_EOL;
	}
