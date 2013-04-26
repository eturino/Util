<?php

class EtuDev_Util_Spatial
{

    /**
     * based on: http: //www.marketingtechblog.com/calculate-distance/#ixzz22IGixa7M
     *
     * @param float $latitude1
     * @param float $longitude1
     * @param float $latitude2
     * @param float $longitude2
     * @param bool  $km
     *
     * @return float
     */
    static protected function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $km = true)
    {
        if ($latitude1 == $latitude2 && $longitude1 == $longitude2) {
            return 0.0;
        }

        $theta    = $longitude1 - $longitude2;
        $thetarad = deg2rad($theta);

        $lat1rad = deg2rad($latitude1);
        $lat2rad = deg2rad($latitude2);

        $distance = (sin($lat1rad) * sin($lat2rad)) + (cos($lat1rad) * cos($lat2rad) * cos($thetarad));
        $distance = acos($distance);
        $distance = rad2deg($distance);
//		$distance = $distance * 60 * 1.1515;
//		if ($km) {
//			$distance = $distance * 1.609344;
//		}
        $distance = $km ? $distance * 111.18957696 : $distance * 69.09;

        return round($distance, 4);
    }


    static public function getDistanceMiles($lat1, $lon1, $lat2, $lon2)
    {
        return static::getDistanceBetweenPointsNew($lat1, $lon1, $lat2, $lon2, false);
    }

    static public function getDistanceKMs($lat1, $lon1, $lat2, $lon2)
    {
        return static::getDistanceBetweenPointsNew($lat1, $lon1, $lat2, $lon2, true);
    }

}
