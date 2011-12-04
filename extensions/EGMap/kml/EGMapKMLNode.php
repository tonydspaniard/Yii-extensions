<?php
/**
 * 
 * EGMapKMLNode Class 
 * 
 * KML node base class
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
class EGMapKMLNode {
	/**
	 * 
	 * Tag name
	 * @var string
	 */
	public 		$tag; 
	/**
	 * 
	 * Tag id
	 * @var string
	 */
	public		$tagId; 
	/**
	 * 
	 * Tag content
	 * @var string
	 */
	public 		$content; 
	/**
	 * 
	 * Tag attributes
	 * @var array
	 */
	public 		$attributes = array();
	/**
	 * 
	 * Child nodes collection
	 * @var array $nodes
	 */
	protected 	$nodes = array(); // children
	/**
	 * 
	 * Class constructor
	 * @param string $tag name
	 * @param string $content
	 */
	public function __construct($tag, $content = null){
		$this->tag 		= $tag;
		$this->content 	= $content;
	}
	/**
	 * 
	 * Adds a child node to the tag
	 * @param EGMapKMLNode $node
	 */
	public function addChild( EGMapKMLNode $node ){
		$this->nodes[] = $node;
	}
	/**
	 * 
	 * Clears all added children
	 */
	public function clearChildren(){
		 $this->nodes = array();
	}
	/**
	 * 
	 * @return well formatted XML KML tags
	 */
	public function toXML(){
		$result = '';
		
		if (is_array($this->attributes)) 
			$this->attributes['id'] = $this->tagId;
		else $this->attributes = array();
		
		$result .= CHtml::openTag($this->tag, (is_array($this->attributes)? $this->attributes:array()));
		if(null !== $this->content && !empty($this->content))
		{
			if($this->tag === 'description'){
				$result .= '<![CDATA['. $this->content.']]>';
			}
			else if(is_array($this->content)){
				// arrays are separated by carriage return
				// they can also be separated by spaces
				$result .= implode(PHP_EOL, $this->content); 
			}
			else 
				$result .= $this->content;
		}	
		$result .= $this->renderChildren();
		$result .= CHtml::closeTag($this->tag);
		
		return $result;
	}
	/**
	 * 
	 * Renders children tags
	 */
	protected function renderChildren()
	{
		$children = '';
		if( isset($this->nodes) && is_array($this->nodes) ){
			foreach( $this->nodes as $node )
				$children .= $node->toXML();
		}
		return $children;
	}
	/**
	 * 
	 * Checks if a node name property has null value
	 * if not then create a node 
	 * @param string $node
	 */
	protected function checkNode( $node ){
		if(!is_null($this->$node))
			$this->nodes[] = new EGMapKMLNode($node, $this->$node);
	}
}