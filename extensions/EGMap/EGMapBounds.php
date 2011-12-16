<?php

/**
 * 
 * EGMapBounds Class
 * Modified by Antonio Ramirez Cobos
 * for Yii to integrate class as Extension
 * 
 * @since 2010-12-22 Antonio Ramirez
 * @link http://www.ramirezcobos.com
 * 
 * GoogleMap Bounds
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
class EGMapBounds {

	/**
	 * 
	 * South West EGMapCoord
	 * @var EGMapCoord
	 */
	protected $sw = null;

	/**
	 * 
	 * North East EGMapCoord
	 * @var EGMapCoord
	 */
	protected $ne = null;

	/**
	 * Create a new Bounds object
	 *
	 * @param EGMapCoord $nw
	 * @param EGMapCoord $se
	 */
	public function __construct(EGMapCoord $sw = null, EGMapCoord $ne = null)
	{
		if (is_null($sw))
		{
			$sw = new EGMapCoord();
		}
		if (is_null($ne))
		{
			$ne = new EGMapCoord();
		}
		$this->sw = $sw;
		$this->ne = $ne;
	}

	/**
	 * 
	 * @return EGMapCoord object
	 */
	public function getNorthEast()
	{

		return $this->ne;
	}

	/**
	 * 
	 * @return EGMapCoord object
	 */
	public function getSouthWest()
	{
		return $this->sw;
	}

	/**
	 * 
	 * Creates EGMapCoords (bounds) from a string representation
	 * of Lat,Lon values
	 * @param string $string ((48.82415805606007,%202.308330535888672),%20(48.867086142850226,%202.376995086669922))
	 */
	static public function createFromString($string)
	{
		preg_match('/\(\((.*?)\), \((.*?)\)\)/', $string, $matches);
		if (count($matches) == 3)
		{
			$sw = EGMapCoord::createFromString($matches[1]);
			$ne = EGMapCoord::createFromString($matches[2]);
			if (!is_null($sw) && !is_null($ne))
			{

				return new EGMapBounds($sw, $ne);
			}

			return null;
		}
	}

	/**
	 * Google String representations
	 *
	 * @return string
	 * @author fabriceb
	 * @since Feb 17, 2009 fabriceb
	 */
	public function __toString()
	{

		return '((' . $this->getSouthWest()->getLatitude() . ', ' . $this->getSouthWest()->getLongitude() . '), (' . $this->getNorthEast()->getLatitude() . ', ' . $this->getNorthEast()->getLongitude() . '))';
	}
	
	/**
	 *
	 * @return string LatLngBounds constructor
	 */
	public function toJs()
	{
		return 'new google.maps.LatLngBounds('.$this->getSouthWest()->toJs().','.$this->getNorthEast()->toJs().')';
	}

	/**
	 * Get the latitude of the center of the zone
	 *
	 * @return integer
	 * @author fabriceb
	 * @since 2008-12-03 
	 */
	public function getCenterLat()
	{
		if (is_null($this->getSouthWest()) || is_null($this->getNorthEast()))
		{

			return null;
		}

		return floatval(($this->getSouthWest()->getLatitude() + $this->getNorthEast()->getLatitude()) / 2);
	}

	/**
	 * Get the longitude of the center of the zone
	 *
	 * @return integer
	 * @author fabriceb
	 * @since 2008-12-03 
	 */
	public function getCenterLng()
	{
		if (is_null($this->getSouthWest()) || is_null($this->getNorthEast()))
		{

			return null;
		}

		return floatval(($this->getSouthWest()->getLongitude() + $this->getNorthEast()->getLongitude()) / 2);
	}

	/**
	 * Get the coordinates of the center of the zone
	 *
	 * @return EGMapCoord
	 * @author fabriceb
	 * @since 2008-12-03 
	 */
	public function getCenterCoord()
	{

		return new EGMapCoord($this->getCenterLat(), $this->getCenterLng());
	}

	/**
	 * Hauteur du carré
	 *
	 * @return float
	 * @author fabriceb
	 * @since Feb 17, 2009 fabriceb
	 */
	public function getHeight()
	{

		return abs($this->getNorthEast()->getLatitude() - $this->getSouthWest()->getLatitude());
	}

	/**
	 * Largeur du carré
	 *
	 * @return float
	 * @author fabriceb
	 * @since Feb 17, 2009 fabriceb
	 */
	public function getWidth()
	{

		return abs($this->getNorthEast()->getLongitude() - $this->getSouthWest()->getLongitude());
	}

	/**
	 * Does a homthety transformtion on the bounds, centered on the center of the bounds
	 *
	 * @param float $factor
	 * @return EGMapBounds $bounds
	 * @author fabriceb
	 * @since Feb 17, 2009 fabriceb
	 */
	public function getHomothety($factor)
	{
		$bounds = new EGMapBounds();
		$lat = $this->getCenterLat();
		$lng = $this->getCenterLng();
		$bounds->getNorthEast()->setLatitude($factor * $this->getNorthEast()->getLatitude() + $lat * (1 - $factor));
		$bounds->getSouthWest()->setLatitude($factor * $this->getSouthWest()->getLatitude() + $lat * (1 - $factor));
		$bounds->getNorthEast()->setLongitude($factor * $this->getNorthEast()->getLongitude() + $lng * (1 - $factor));
		$bounds->getSouthWest()->setLongitude($factor * $this->getSouthWest()->getLongitude() + $lng * (1 - $factor));

		return $bounds;
	}

	/**
	 * gets zoomed out bounds
	 *
	 * @param integer $zoom_coef
	 * @return EGMapBounds
	 * @author fabriceb
	 * @since Feb 18, 2009 fabriceb
	 */
	public function getZoomOut($zoom_coef)
	{
		if ($zoom_coef > 0)
		{
			$bounds = $this->getHomothety(pow(2, $zoom_coef));

			return $bounds;
		}

		return $this;
	}

	/**
	 * Returns the most appropriate zoom to see the bounds on a map with min(width,height) = $min_w_h
	 *
	 * @param integer $min_w_h width or height of the map in pixels
	 * @return integer
	 * @author fabriceb
	 * @since Feb 18, 2009 fabriceb
	 */
	public function getZoom($min_w_h, $default_zoom = 14)
	{
		$infinity = 999999999;
		$factor_h = $infinity;
		$factor_w = $infinity;

		/*

		  formula: the width of the bounds in "pixels" is pix_w * 2^z
		  We want pix_w * 2^z to fit in min_w_h so we are looking for
		  z = round ( log2 ( min_w_h / pix_w  ) )
		 */

		$sw_lat_pix = EGMapCoord::fromLatToPix($this->getSouthWest()->getLatitude(), 0);
		$ne_lat_pix = EGMapCoord::fromLatToPix($this->getNorthEast()->getLatitude(), 0);
		$pix_h = abs($sw_lat_pix - $ne_lat_pix);
		if ($pix_h > 0)
		{
			$factor_h = $min_w_h / $pix_h;
		}

		$sw_lng_pix = EGMapCoord::fromLngToPix($this->getSouthWest()->getLongitude(), 0);
		$ne_lng_pix = EGMapCoord::fromLngToPix($this->getNorthEast()->getLongitude(), 0);
		$pix_w = abs($sw_lng_pix - $ne_lng_pix);
		if ($pix_w > 0)
		{
			$factor_w = $min_w_h / $pix_w;
		}

		$factor = min($factor_w, $factor_h);

		// bounds is one point, no zoom can be determined
		if ($factor == $infinity)
		{

			return $default_zoom;
		}

		return round(log($factor, 2));
	}

	/**
	 *
	 * Returns the boundaries that the others have
	 * 
	 * @param EGMapBounds[] $boundss
	 * @param float $margin
	 * @return EGMapBounds
	 * @author fabriceb
	 * @since Feb 18, 2009 fabriceb
	 */
	public static function getBoundsContainingAllBounds($boundss, $margin = 0)
	{
		$min_lat = 1000;
		$max_lat = -1000;
		$min_lng = 1000;
		$max_lng = -1000;
		foreach ($boundss as $bounds)
		{
			$min_lat = min($min_lat, $bounds->getSouthWest()->getLatitude());
			$min_lng = min($min_lng, $bounds->getSouthWest()->getLongitude());
			$max_lat = max($max_lat, $bounds->getNorthEast()->getLatitude());
			$max_lng = max($max_lng, $bounds->getNorthEast()->getLongitude());
		}

		if ($margin > 0)
		{
			$min_lat = $min_lat - $margin * ($max_lat - $min_lat);
			$min_lng = $min_lng - $margin * ($max_lng - $min_lng);
			$max_lat = $max_lat + $margin * ($max_lat - $min_lat);
			$max_lng = $max_lng + $margin * ($max_lng - $min_lng);
		}

		$bounds = new EGMapBounds(new EGMapCoord($min_lat, $min_lng), new EGMapCoord($max_lat, $max_lng));
		return $bounds;
	}

	/**
	 * Retuns bounds containg an array of coordinates
	 *
	 * @param EGMapCoord[] $coords
	 * @param float $margin
	 * @return EGMapBounds
	 * @author fabriceb
	 * @since Mar 13, 2009 fabriceb
	 */
	public static function getBoundsContainingCoords($coords, $margin = 0)
	{
		$min_lat = 1000;
		$max_lat = -1000;
		$min_lng = 1000;
		$max_lng = -1000;
		foreach ($coords as $coord)
		{
			/* @var $coord EGMapCoord */
			$min_lat = min($min_lat, $coord->getLatitude());
			$max_lat = max($max_lat, $coord->getLatitude());
			$min_lng = min($min_lng, $coord->getLongitude());
			$max_lng = max($max_lng, $coord->getLongitude());
		}

		if ($margin > 0)
		{
			$min_lat = $min_lat - $margin * ($max_lat - $min_lat);
			$min_lng = $min_lng - $margin * ($max_lng - $min_lng);
			$max_lat = $max_lat + $margin * ($max_lat - $min_lat);
			$max_lng = $max_lng + $margin * ($max_lng - $min_lng);
		}
		$bounds = new EGMapBounds(new EGMapCoord($min_lat, $min_lng), new EGMapCoord($max_lat, $max_lng));

		return $bounds;
	}

	/**
	 *
	 * @param GMapMarker[] $markers array of Markers
	 * @param float $margin margin factor for the bounds
	 * @return EGMapBounds
	 * @author fabriceb
	 * @since 2009-05-02
	 * @since 2011-01-25 modified by Antonio Ramirez
	 *
	 * */
	public static function getBoundsContainingMarkers($markers, $margin = 0)
	{
		$coords = array();
		foreach ($markers as $marker)
		{
			array_push($coords, $marker->position);
		}

		return EGMapBounds::getBoundsContainingCoords($coords, $margin);
	}

	/**
	 *
	 * @param GMapPolygon[] $polygons array of Polygons
	 * @param float $margin margin factor for the bounds
	 * @return EGMapBounds
	 * @author Matt Kay
	 * @since 2011-03-10
	 * 	Added this function based on getBoundsContainingMarkers
	 *
	 * */
	public static function getBoundsContainingPolygons($polygons, $margin = 0)
	{
		$coords = array();
		foreach ($polygons as $polygon)
		{
			// merge LatLng arrays
			array_merge($coords, $polygon->getCoords());
		}

		return EGMapBounds::getBoundsContainingCoords($polygon->getCoords(), $margin);
	}
	
	/**
	 * Calculate the bounds corresponding to a specific center and zoom level for a give map size in pixels
	 * 
	 * @param EGMapCoord $center_coord
	 * @param integer $zoom
	 * @param integer $width
	 * @param integer $height
	 * @return EGMapBounds
	 * @author fabriceb
	 * @since Jun 2, 2009 fabriceb
	 */
	public static function getBoundsFromCenterAndZoom(EGMapCoord $center_coord, $zoom, $width, $height = null)
	{
		if (is_null($height))
		{
			$height = $width;
		}

		$center_lat = $center_coord->getLatitude();
		$center_lng = $center_coord->getLongitude();

		$pix = EGMapCoord::fromLatToPix($center_lat, $zoom);
		$ne_lat = EGMapCoord::fromPixToLat($pix - round(($height - 1) / 2), $zoom);
		$sw_lat = EGMapCoord::fromPixToLat($pix + round(($height - 1) / 2), $zoom);

		$pix = EGMapCoord::fromLngToPix($center_lng, $zoom);
		$sw_lng = EGMapCoord::fromPixToLng($pix - round(($width - 1) / 2), $zoom);
		$ne_lng = EGMapCoord::fromPixToLng($pix + round(($width - 1) / 2), $zoom);

		return new EGMapBounds(new EGMapCoord($sw_lat, $sw_lng), new EGMapCoord($ne_lat, $ne_lng));
	}

	/**
	 * 
	 * @param EGMapCoord $gmap_coord
	 * @return boolean $is_inside
	 * @author fabriceb
	 * @since Jun 2, 2009 fabriceb
	 */
	public function containsEGMapCoord(EGMapCoord $gmap_coord)
	{
		$is_inside =
			(
			$gmap_coord->getLatitude() < $this->getNorthEast()->getLatitude()
			&&
			$gmap_coord->getLatitude() > $this->getSouthWest()->getLatitude()
			&&
			$gmap_coord->getLongitude() < $this->getNorthEast()->getLongitude()
			&&
			$gmap_coord->getLongitude() > $this->getSouthWest()->getLongitude()
			);

		return $is_inside;
	}

}
