<?php

/**
 * 
 * EGMapMarkerImage
 * 
 * Modified by Antonio Ramirez
 * @link www.ramirezcobos.com
 * 
 * Change log:
 * @since 2011-01-22 Antonio Ramirez
 * - Implemented EGMap support for object to js translation
 * - Modified internal properties from arrays to objects
 * - Modified all functions to work with newly adopted objects
 * 
 * A GoogleMap MarkerImage
 * @author Maxime Picaud
 * 
 * 
 * @copyright 
 * info as this library is part of the library made by Fabrice Bernhard 
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
class EGMapMarkerImage
{
  //String $url url of image
  protected $url;
   
  // EGMapSize
  protected $size;
   
  // EGMapPoint 
  protected $origin;
  
  // EGMapPoint 
  protected $anchor; 
  
  /**
   * @param string $js_name Javascript name of the marker
   * @param string $url url of image 
   * @param array $size array('width' => $width,'height' => $height)
   * @param EGMapPoint $origin 
   * @param EGMapPoint $anchor
   * @author Maxime Picaud
   * @since 4 sept. 2009
   * @since 2011-01-21 modified by Antonio Ramirez Cobos
   */
  public function __construct( $url, EGMapSize $size=null, EGMapPoint $origin=null, EGMapPoint $anchor=null)
  {
    $this->url = $url;
    
    if(null !== $size) $this->size = $size;
    
    if( null !== $origin)  $this->origin = $origin;

  	if( null !== $anchor ) $this->anchor = $anchor;
    
  }
  
  /**
   * 
   * @return url of the image
   * @author Antonio Ramirez
   */
  public function getUrl()
  {
    return $this->url;
  }
  
  /**
   * 
   * @return EGMapSize | null
   * @author Antonio Ramirez
   */
  public function getSize()
  {
    return $this->size;
  }
  
  /**
   * 
   * @return numeric string | number size width
   * @author Antonio Ramirez
   */
  public function getWidth()
  {
    if( $this->size !== null )
    	return $this->size->getWidth;
  }
  
  /**
   * 
   * @return numeric string |Ênumber $height
   * @author Maxime Picaud
   * @since 4 sept. 2009
   * @since 2011-01-22 by Antonio Ramirez
   */
  public function getHeight()
  {
  	if( $this->size !== null )
    	return $this->size->getHeight;
  }
  
  
  /**
   * 
   * @param numeric string | number $width
   * @param numeric string | number $height
   * @author Antonio Ramirez
   * @since 2011-01-22
   */
  public function setSize($width,$height)
  {
  	if( null === $this->size )
  		$this->size = new EGMapSize();
    $this->size->setWidth($width);
    $this->size->setHeight($height);
  }
  
  /**
   * 
   * @return EGMapPoint $anchor
   * @author Maxime Picaud
   * @since 4 sept. 2009
   * @since 2011-01-21 Modified by Antonio Ramirez
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  
  /**
   * 
   * @return numeric string | number origin CoordX
   * @author Antonio Ramirez
   */
  public function getOriginX()
  {
  	if( $this->origin !== null )
  		return $this->origin->getCoordX();

  }
  
  /**
   * 
   * @return numeric string | number origin CoordY
   * @author Antonio Ramirez
   */
  public function getOriginY()
  {
	if( $this->origin !== null )
  		return $this->origin->getCoordY();
  }
  
  /**
   * 
   * @param numeric string | number origin CoordX
   * @param numeric string | number origin CoordY
   * @author Antonio Ramirez
   */
  public function setOrigin( $x, $y)
  {
    if( null === $this->origin )
  		$this->origin = new EGMapPoint();
    $this->origin->setCoordX($x);
    $this->origin->setCoordY($y);
  }
  
  /**
   * 
   * @return EGMapPoint $anchor
   * @author Maxime Picaud
   * @since 4 sept. 2009
   * @since 2011-01-21 Modified by Antonio Ramirez
   */
  public function getAnchor()
  {
    return $this->anchor;
  }
  
  /**
   * 
   * @return numeric string | number anchor CoordX
   * @author Antonio Ramirez
   */
  public function getAnchorX()
  {
  	if( $this->anchor !== null )
  		return $this->anchor->getCoordX();
  }
  
  /**
   * 
   * @return numeric string | number anchor CoordY
   * @author Antonio Ramirez
   */
  public function getAnchorY()
  {
  	if( $this->anchor !== null )
  		return $this->anchor->getCoordY();
  }
  
  /**
   * 
   * @param numeric string | number anchor CoordX
   * @param numeric string | number anchor CoordY
   * @author Antonio Ramirez
   */
  public function setAnchor($x,$y)
  {
  	if( null === $this->anchor )
  		$this->anchor = new EGMapPoint();
    $this->anchor->setCoordX($x);
    $this->anchor->setCoordY($y);
  }

  /**
   * 
   * @return string js code to create the markerImage
   * @author Maxime Picaud
   * @since 4 sept. 2009
   * @since 2011-01-22 modified by Antonio Ramirez
   * 		implemented EGMap support for object to 
   * 		js translation
   */
  public function toJs()
  {
    $params = array();
    
    $params[] = '"'.$this->getUrl().'"';
    $params[] = EGMap::encode($this->size); 
    $params[] = EGMap::encode($this->origin);
    $params[] = EGMap::encode($this->anchor);
    
    $return = 'new google.maps.MarkerImage('.implode(',',$params).")";
    
    return $return;
  }

}
