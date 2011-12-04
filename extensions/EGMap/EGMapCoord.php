<?php
/**
 * 
 * EGMapCoord Class
 * Modified by Antonio Ramirez Cobos
 * to be integrated to Yii as an extension
 * @link http://www.ramirezcobos.com
 * @since 2010-12-22 
 * 
 * GoogleMap Coords
 * @author Fabrice Bernhard
  * 
 * @copyright 
 * info as this library is a modified version of Fabrice Bernhard 
 * 
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software 
 * and associated documentation files (the "Software"), to deal in the Software without restriction, 
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, 
 * subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial 
 * portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
 * LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
 * NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE 
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */
class EGMapCoord
{
	 const EARTH_RADIUS = 6380;
	  /**
	   * Latitude
	   *
	   * @var float
	   */
	  protected $latitude;
	  /**
	   * Longitude
	   *
	   * @var float
	   */
	  protected $longitude;
	
	  public function __construct($latitude = null, $longitude = null)
	  {
	    $this->latitude     = floatval($latitude);
	    $this->longitude    = floatval($longitude);
	  }
	
	
	  /**
	   * @return float
	   *
	   * @author fabriceb
	   * @since Apr 21, 2010
	   */
	  public function getLatitude()
	  {
	
	    return (float) $this->latitude;
	  }
	
	  /**
	   * @return float
	   *
	   * @author fabriceb
	   * @since Apr 21, 2010
	   */
	  public function getLongitude()
	  {
	
	    return (float) $this->longitude;
	  }
	
	  /**
	   *
	   * @param float $latitude
	   * @author fabriceb
	   * @since Apr 21, 2010
	   */
	  public function setLatitude($latitude)
	  {
	    $this->latitude = floatval($latitude);
	  }
	
	  /**
	   *
	   * @param float $latitude
	   * @author fabriceb
	   * @since Apr 21, 2010
	   */
	  public function setLongitude($longitude)
	  {
	    $this->longitude = floatval($longitude);
	  }
	
	  /**
	   *
	   * @param $string
	   * @return EGMapCoord
	   * @author fabriceb
	   */
	  public static function createFromString($string)
	  {
	    $coord_array = explode(',',$string);
	    if (count($coord_array)==2)
	    {
	      $latitude = floatval(trim($coord_array[0]));
	      $longitude = floatval(trim($coord_array[1]));
	
	      return new EGMapCoord($latitude,$longitude);
	    }
	
	    return null;
	  }
	
	  /**
	   *
	   * @return string
	   */
	  public function toJs( )
	  {
	    return 'new google.maps.LatLng('.$this->__toString().')';
	  }
	
	  /**
	   * Lng to Pix
	   * cf. a World's map according to Google http://mt0.google.com/mt/v=ap.92&hl=en&x=0&y=0&z=0&s=
	   *
	   * @param float $lng
	   * @param integer $zoom
	   * @return integer
	   * @author fabriceb
	   * @since Feb 18, 2009 fabriceb
	   */
	  public static function fromLngToPix($lng,$zoom)
	  {
	    $lngrad = deg2rad($lng);
	    $mercx = $lngrad;
	    $cartx = $mercx + pi();
	    $pixelx = $cartx * 256/(2*pi());
	    $pixelx_zoom =  $pixelx * pow(2,$zoom);
	
	    return $pixelx_zoom;
	  }
	
	  /**
	   * Lat to Pix
	   * cf. a World's map according to Google http://mt0.google.com/mt/v=ap.92&hl=en&x=0&y=0&z=0&s=
	   *
	   * @param float $lat
	   * @param integer $zoom
	   * @return integer
	   * @author fabriceb
	   * @since Feb 18, 2009 fabriceb
	   */
	  public static function fromLatToPix($lat,$zoom)
	  {
	    if ($lat == 90)
	    {
	      $pixely = 0;
	    }
	    else if ($lat == -90)
	    {
	      $pixely = 256;
	    }
	    else
	    {
	      $latrad = deg2rad($lat);
	      $mercy = log(tan(pi()/4+$latrad/2));
	      $carty = pi() - $mercy;
	      $pixely = $carty * 256 / 2 / pi();
	      $pixely = max(0, $pixely); // correct rounding errors near north and south poles
	      $pixely = min(256, $pixely); // correct rounding errors near north and south poles
	    }
	    $pixely_zoom = $pixely * pow(2,$zoom);
	
	    return $pixely_zoom;
	  }
	
	  /**
	   * Pix to Lng
	   * cf. a World's map according to Google http://mt0.google.com/mt/v=ap.92&hl=en&x=0&y=0&z=0&s=
	   *
	   * @param integer $pix
	   * @param integer $zoom
	   * @return float
	   * @author fabriceb
	   * @since Feb 18, 2009 fabriceb
	   */
	  public static function fromPixToLng($pixelx_zoom,$zoom)
	  {
	    $pixelx = $pixelx_zoom / pow(2,$zoom);
	    $cartx = $pixelx / 256 * 2 * pi();
	    $mercx = $cartx - pi();
	    $lngrad = $mercx;
	    $lng = rad2deg($lngrad);
	
	    return $lng;
	  }
	
	  /**
	   * Pix to Lat
	   * cf. a World's map according to Google http://mt0.google.com/mt/v=ap.92&hl=en&x=0&y=0&z=0&s=
	   *
	   * @param integer $pix
	   * @param integer $zoom
	   * @return float
	   * @author fabriceb
	   * @since Feb 18, 2009 fabriceb
	   */
	  public static function fromPixToLat($pixely_zoom,$zoom)
	  {
	    $pixely = $pixely_zoom / pow(2,$zoom);
	    if ($pixely == 0)
	    {
	      $lat = 90;
	    }
	    else if ($pixely == 256)
	    {
	      $lat = -90;
	    }
	    else
	    {
	      $carty = $pixely / 256 * 2 * pi();
	      $mercy = pi() - $carty;
	      $latrad = 2 * atan(exp($mercy))-pi()/2;
	      $lat = rad2deg($latrad);
	    }
	
	    return $lat;
	  }
	
	  /**
	   * Calculates the center of an array of coordiantes
	   *
	   * @param EGMapCoord[] $coords
	   * @return EGMapCoord
	   * @author fabriceb
	   * @since 2009-05-02
	   */
	  public static function getMassCenterCoord($coords)
	  {
	    if (count($coords)==0)
	    {
	
	      return null;
	    }
	    $center_lat = 0;
	    $center_lng = 0;
	    foreach($coords as $coord)
	    {
	      /* @var $coord EGMapCoord */
	      $center_lat += $coord->getLatitude();
	      $center_lng += $coord->getLongitude();
	    }
	
	    return new EGMapCoord($center_lat/count($coords),$center_lng/count($coords));
	  }
	
	  /**
	   * Calculates the center of an array of coordiantes
	   *
	   * @param EGMapCoord[] $coords
	   * @return EGMapCoord
	   * @author fabriceb
	   * @since 2009-05-02
	   */
	  public static function getCenterCoord($coords)
	  {
	    $bounds = EGMapBounds::getBoundsContainingCoords($coords);
	
	    return $bounds->getCenterCoord();
	  }
	
	  /**
	   * toString method
	   *
	   * @return string
	   *
	   * @author fabriceb
	   * @since 2009-05-02
	   * @since 2010-04-21 added (float) to force . instead of , as separator. If still not working use number_format($this->getLongitude(), 10, '.', '');
	   */
	  public function __toString()
	  {
	
	    return ((float) $this->getLatitude()).', '.((float) $this->getLongitude());
	  }
	
	  /**
	   * very approximate calculation of the distance in kilometers between two coordinates
	   *
	   * @param EGMapCoord $coord2
	   * @return float
	   *
	   * @author fabriceb
	   * @since 2009-05-03
	   */
	  public function distanceFrom($coord2)
	  {
	    $lat_dist = abs($this->getLatitude()-$coord2->getLatitude());
	    $lng_dist = abs($this->getLongitude()-$coord2->getLongitude());
	
	    $rad_dist = deg2rad(sqrt(pow($lat_dist,2)+pow($lng_dist,2)));
	
	    return $rad_dist * self::EARTH_RADIUS;
	  }
	
	  /**
	   * exact distance with spherical law of cosines
	   *
	   * @param EGMapCoord $coord2
	   * @return float
	   * @see http://www.zipcodeworld.com/samples/distance.php.html
	   *
	   * @author fabriceb
	   * @since Apr 21, 2010
	   */
	  public function exactDistanceSLCFrom($coord2)
	  {
	    $lat1 = $this->getLatitude();
	    $lat2 = $coord2->getLatitude();
	    $lon1 = $this->getLongitude();
	    $lon2 = $coord2->getLongitude();
	
	    $theta = $lon1 - $lon2;
	    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	    $dist = acos($dist);
	    $dist = rad2deg($dist);
	    $miles = $dist * 60 * 1.1515;
	
	    return $miles * 1.609344;
	  }
	
	 /**
	   * exact distance with Haversine formula
	   *
	   * @param EGMapCoord $coord2
	   * @return float
	   * @see http://www.movable-type.co.uk/scripts/latlong.html
	   *
	   * @author fabriceb
	   * @since Apr 21, 2010
	   */
	  public function exactDistanceFrom($coord2)
	  {
	    $lat1 = deg2rad($this->getLatitude());
	    $lat2 = deg2rad($coord2->getLatitude());
	    $lon1 = deg2rad($this->getLongitude());
	    $lon2 = deg2rad($coord2->getLongitude());
	
	    $dLatHalf = ($lat2 - $lat1) / 2;
	    $dLonHalf = ($lon2 - $lon1) / 2;
	
	    $a = pow(sin($dLatHalf), 2) + cos($lat1) * cos($lat2) * pow(sin($dLonHalf), 2);
	    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
	
	    return $c * self::EARTH_RADIUS;
	  }
	
	  /**
	   * very approximate calculation of the distance in kilometers between two coordinates
	   *
	   * @param EGMapCoord $coord1
	   * @param EGMapCoord $coord2
	   * @return float
	   *
	   * @author fabriceb
	   * @since 2009-05-03
	   */
	  public static function distance($coord1, $coord2)
	  {
	
	    return $coord1->distanceFrom($coord2);
	  }
	
	  /**
	   * exact distance with spherical law of cosines
	   *
	   * @param EGMapCoord $coord1
	   * @param EGMapCoord $coord2
	   * @return float
	   * @see exactDistanceSLCFrom
	   *
	   * @author fabriceb
	   * @since Apr 21, 2010
	   */
	  public static function exactDistanceSLC($coord1, $coord2)
	  {
	
	    return $coord1->exactDistanceSLCFrom($coord2);
	  }
	
	  /**
	   * exact distance with Haversine formula
	   *
	   * @param EGMapCoord $coord1
	   * @param EGMapCoord $coord2
	   * @return float
	   * @see exactDistanceFrom
	   *
	   * @author fabriceb
	   * @since Apr 21, 2010
	   */
	  public static function exactDistance($coord1, $coord2)
	  {
	
	    return $coord1->exactDistanceFrom($coord2);
	  }
	
	  /**
	   *
	   * @param EGMapBounds $gmap_bounds
	   * @return boolean $is_inside
	   *
	   * @author fabriceb
	   * @since Jun 2, 2009 fabriceb
	   */
	  public function isInsideBounds(EGMapBounds $gmap_bounds)
	  {
	
	    return $gmap_bounds->containsGMapCoord($this);
	  }
}