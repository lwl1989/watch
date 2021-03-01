<?php
namespace App\Library;

class Distance
{
	public static function getDistance($lng1, $lat1, $lng2, $lat2) : int
	{
		$x = ($lng2 - $lng1) * M_PI * 6371004 * cos (($lat1+$lat2) / 2 * M_PI / 180) / 180;
		$y = ($lat2 - $lat1) * M_PI * 6371004 / 180;

		return intval(sqrt($x * $x + $y * $y));
	}
}