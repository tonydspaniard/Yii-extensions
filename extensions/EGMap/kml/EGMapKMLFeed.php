<?php
/**
 * 
 * EGMapKMLFeed Class 
 * 
 * KML Feed Creator
 *
 * @author Antonio Ramirez Cobos
 * @link www.ramirezcobos.com
 *
 * 
 * @copyright 
 * 
 * Copyright (c) 2010 Antonio Ramirez Cobos
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
/**
 * 
 * List of currently supported KML tags by Google Maps v3
 * ----------------------------------------------
 * <kml>
 * <atom:author>
 * <atom:link>
 * <atom:name>
 * -----------------------------------------------
 * Example
 * ------------------------------------------------
   <Document>
  		<atom:author>             
      		<atom:name>J. K. Rowling</atom:name>         
    	</atom:author>         
	    <atom:link href="http://www.harrypotter.com" />
	    <Style id="myStyleId"></Style>
	    <Placemark></Placemark>
   </Document>
   ------------------------------------------------
 * <Placemark>
 * <styleUrl>
 * <name>
 * <description>
 * <Point>
 *    <coordinates>
 * ------------------------------------------------
 * Example
 * ------------------------------------------------
    <Placemark>
   	   <styleUrl>#myIconStyleID</styleUrl>
	   <name>
	   <description>
	  	<![CDATA[
			This is an image 
			<img src="icon.jpg"> 
			and we have a link http://www.google.com.
	  	]]>
		</description>
		<Point>
	  		<coordinates>-90.86948943473118,48.25450093195546</coordinates>
		</Point>
	</Placemark>
 * ------------------------------------------------
 * <Style>
 * 	<IconStyle id="myIconStyleID>
 * 		<Icon>
 *  <PolyStyle id="myPolyStyleID">
 *      <color>
 *      <colorMode>
 *  <LineStyle>
 *      <color>
 *      <width>
 * 
 * 
 * ------------------------------------------------
 * Examples
 * ------------------------------------------------
  <Document>
    <Style id="restaurantStyle">
      <IconStyle id="restuarantIcon">
      <Icon>
        <href>http://maps.google.com/mapfiles/kml/pal2/icon63.png</href>
      </Icon>
      </IconStyle>
    </Style>
   ------------------------------------------------
    <Style id="examplePolyStyle">
      <PolyStyle>
        <color>ff0000cc</color>
        <colorMode>random</colorMode>
      </PolyStyle>
    </Style>
   ------------------------------------------------
    <Style id="linestyleExample">
      <LineStyle>
        <color>7f0000ff</color>
        <width>4</width>
      </LineStyle>
    </Style>
  </Document>
 * ------------------------------------------------	
 * <LatLonBox>	
 *   <north>
 *   <south>
 *   <east>
 *   <west>
 * ------------------------------------------------
 * Example
 * ------------------------------------------------
   <Document>
  	<LatLonBox>
	   <north>48.25475939255556</north>
	   <south>48.25207367852141</south>
	   <east>-90.86591508839973</east>
	   <west>-90.8714285289695</west>
	</LatLonBox>
   </Document>
   ------------------------------------------------
 * <NetworkLink>
 *  <Link>
 *    <refreshMode>
 *    <refreshInterval>
 *    <viewRefreshTime>
 * ------------------------------------------------
 * Example
 * ------------------------------------------------
 	<Document>
 	<NetworkLink>
  	  <name>NE US Radar</name>
	  <Link>
	    <href>http://www.example.com/geotiff/NE/MergedReflectivityQComposite.kml</href>
	    <refreshMode>onInterval</refreshMode> // headers HTTP only compatible with mode "onExpire"
	    <refreshInterval>30</refreshInterval>
	    <viewRefreshMode>onStop</viewRefreshMode>
	    <viewRefreshTime>7</viewRefreshTime>
	  </Link>
	</NetworkLink> 
	</Document>
 * ------------------------------------------------
 * <Polygon>
 *   <outerBoundaryIs>
 *     <LinearRing>
 *        <coordinates>
 * <LineString>
 *    <coordinates>
 * ------------------------------------------------
 * Example
 * ------------------------------------------------
   <Placemark>
   <Polygon>
      <innerBoundaryIs> // only supported here
        <LinearRing> 
          <coordinates>
            -122.366212,37.818977,30
            -122.365424,37.819294,30
          </coordinates>
        </LinearRing>
      </innerBoundaryIs>
   </Polygon>
   </Placemark>
  <Placemark>
    <name>unextruded</name>
    <LineString>
      <coordinates>
        -122.364383,37.824664,0 -122.364152,37.824322,0 
      </coordinates>
    </LineString>
  </Placemark>
  ------------------------------------------------
 * Other supported tags by Google Maps v3
 * <range>
 * <ScreenOverlay>
 * <size>	
 * <Snippet>
 * <value>
 * <longitude>
 * <open>	
 * <outline>
 * <expires>
 * <fill>	
 * <heading>	
 * <hotSpot>		
 * <latitude>
 */
class EGMapKMLFeed {
	/**
	 * 
	 * Holds all tags on the feed
	 * @var unknown_type
	 */
	protected $elements;
	
	const STYLE_ICON = 'Icon'; // when using addStyle Array it specifies is an IconStyle
	const STYLE_LINE = 'Line'; // when using addStyle Array it specifies is an LineStyle
	const STYLE_POLY = 'Poly'; // when using addStyle Array it specifies is an PolyStyle
	/**
	 * 
	 * constructor
	 */
	function __construct(){
		$this->elements = new CMap();	
	}
	/**
	 * 
	 * ATOM style author
	 * @param string $name
	 */
	public function setAuthor( $name ){
		if( null === $this->elements->itemAt('head') )
			$this->elements->add('head', new CMap() );
		$item = '<atom:author><atom:name>'.$name.'</atom:name></atom:author>';
		
		$this->elements->itemAt('head')->add('author', $item);
	  
	}
	/**
	 * 
	 * ATOM style link
	 * @param string $url
	 */
	public function setLink( $url ){
		if( null === $this->elements->itemAt('head') )
			$this->elements->add('head', new CMap() );
		$validator = new CUrlValidator();
		if(!$validator->validateValue($url))
			throw new CException( Yii::t('EGMap', 'EGMapKMLFeed.setLink Url does not seem to valid') );
		$item = '<atom:link href="'.$url.'" />';
		$this->elements->itemAt('head')->add('link', $item);
	}
	/**
	 * 
	 * Adding an externally created Tag at the end of its 'body' section
	 * Note: this method does not validates the tag
	 * @param EGMapKMLNode $tag
	 * @return string id of the inserted Tag
	 */
	public function addTag( EGMapKMLNode $tag ){
		if( null === $this->elements->itemAt('body') )
			$this->elements->add('body', new CMap() );
			
		$name = uniqid();
		
		$this->elements->itemAt('body')->add(uniqid(), $tag->toXML());
		
		return $name;
	}
	/**
	 * 
	 * Removes inserted tag name from specific section
	 * sections can be head, body, styles, and placemark
	 * @param string $tagName to be removed
	 * @param string $section where to remove the tag
	 */
	public function removeTag( $tagName, $section ){
		foreach($this->elements as $map)
		{
			if($map->itemAt($section)){
				$map->itemAt($section)->remove($tagName);
				return true;
			}
		}
		return false;
	}
	/**
	 * 
	 * This method is to add style tags as arrays
	 * the style nodes are represented as the following:
	 * <pre>
	 * // Icon
	 * $nodes = array( 'href'=>'http://url');
	 * // Line
	 * $nodes = array( 'color'=>'#FFAA00','width'=>2);
	 * // Polyline 
	 * $nodes = array( 'color'=>'#FFAA00','colorMode'=>'random' );
	 * </pre>
	 * @param string $styleId id of the style
	 * @param string $styleType the type of the style
	 * @param array $nodes the tags to insert
	 */
	public function addStyleArray( $styleId, $styleType = self::STYLE_ICON, $nodes = array() ){
		$item = '<Style id="'.$styleId.'">';
		switch ($styleType){
		case self::STYLE_ICON:
		case self::STYLE_LINE:
		case self::STYLE_POLY:
			$item .= CHtml::openTag($styleType.'Style');
			break;
		default:
			throw new CException( Yii::t('EGMap','KML Style not supported') );
		}
		$item .= $this->batchNodeCreate($nodes);
		
		$item .= CHtml::closeTag($styleType.'Style');
		$item .= '</Style>';
		
		if( null === $this->elements->itemAt('styles') )
			$this->elements->add( 'styles', new CMap() );
		
		$this->elements->itemAt('styles')->add( $styleId, $item );
	}
	/**
	 * Adds a placemark on array structure
	 * Example:
	 * <pre>
	 * $nodes = array(
	 * 	'name'=>array('content'=>'testing'),
	 *	'description'=>array('content'=>'This marker has <b>HTML</b>'),
	 *	'styleUrl'=>array('content'=>'#style2'),
	 *	'Point'=>array('children'=>array(
	 *		'coordinates'=>array('content'=>'2.9087440013885635,39.719588117933185,0'))));
	 *
	 * 	 $kml->addPlacemark($nodes);
	 * </pre>
	 * @param array $nodes the tags to insert
	 */
	public function addPlacemarkArray( $nodes = array() ){
		$item = '<Placemark>';
		
		$item .= $this->batchNodeCreate($nodes);
		
		$item .= '</Placemark>';
		
		if( null === $this->elements->itemAt('body') )
			$this->elements->add( 'body', new CMap() );
		
		$name = uniqid();
		
		$this->elements->itemAt('body')->add( $name, $item );
		
		return $name;
		
	}
	/**
	 * 
	 * Converts array given tags to its XML representation
	 * Note: It does not check for correct array structure
	 * 
	 * @param array $tags
	 */
	public function batchNodeCreate( $tags ){
		$result = '';
		if(is_array( $tags) ){
			foreach($tags as $tag=>$el){
				$result .= CHtml::openTag($tag, (isset($el['attributes']) && is_array($el['attributes'])? $el['attributes']:array()));
				$result .= isset($el['content'])? ($tag=='description'? '<![CDATA['. $el['content'].']]>':  $el['content']) : '';
				$result .= isset($el['children']) && is_array($el['children'])? $this->batchNodeCreate($el['children']) : '';
				$result .= CHtml::closeTag($tag);
			}
		}
		return $result;
	}
	/**
	 * 
	 * Generates the feed
	 */
	public function generateFeed(  ){
		// you can choose between the both, as both of them work correctly
		// Google Earth MIME/TYPE
		// header('Content-type: application/vnd.google-earth.kml+xml');
		header("Content-type: text/xml");
		
		echo '<kml xmlns="http://earth.google.com/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">';
		echo '<Document>';
		$this->renderItems(array('head', 'body','styles','placemarks'));
		echo '</Document>';
		echo '</kml>';
	}
	/**
	 * 
	 * Render tags by sections
	 * @param array | string sections to render $sections
	 */
	public function renderItems( $sections ){
		
		if(!is_array($sections)) $sections = array( $sections );
		
		foreach( $sections as $section ){
			if(null === $this->elements->itemAt($section)) continue;
			
			foreach($this->elements->itemAt($section) as $tag)
				echo $tag;
		}
	}
}
