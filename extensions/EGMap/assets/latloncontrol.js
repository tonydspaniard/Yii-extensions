/**
* LatLngControl class displays the LatLng and pixel coordinates
* underneath the mouse within a container anchored to it.
* @param {google.maps.Map} map Map to add custom control to.
*/
function LatLngControl(map) {
	/**
	 * Offset the control container from the mouse by this amount.
	 */
	this.ANCHOR_OFFSET_ = new google.maps.Point(8, 8);
	
	/**
	 * Pointer to the HTML container.
	 */
	this.node_ = this.createHtmlNode_();
	
	// Add control to the map. Position is irrelevant.
	map.controls[google.maps.ControlPosition.TOP].push(this.node_);
	
	// Bind this OverlayView to the map so we can access MapCanvasProjection
	// to convert LatLng to Point coordinates.
	this.setMap(map);
	
	// Register an MVC property to indicate whether this custom control
	// is visible or hidden. Initially hide control until mouse is over map.
	this.set('visible', false);
	  }
	  
	  // Extend OverlayView so we can access MapCanvasProjection.
	  LatLngControl.prototype = new google.maps.OverlayView();
	  LatLngControl.prototype.draw = function() {};
	  
	  /**
	   * @private
	   * Helper function creates the HTML node which is the control container.
	   * @return {HTMLDivElement}
	   */
	  LatLngControl.prototype.createHtmlNode_ = function() {
	    var divNode = document.createElement('div');
		    divNode.id = 'latlng-control';
		    divNode.index = 100;
		    divNode.style.backgroundColor = "#ffc";
		    divNode.style.border = "1px solid #676767";
		    divNode.style.fontFamily = "arial, helvetica, sans-serif";
		    divNode.style.fontSize = "0.7em";
		    divNode.style.padding = "2px 4px";
		    divNode.style.position = "absolute";
	    return divNode;
	  };
	  
	  /**
	   * MVC property's state change handler function to show/hide the
	   * control container.
	   */
	  LatLngControl.prototype.visible_changed = function() {
	    this.node_.style.display = this.get('visible') ? '' : 'none';
	  };
	  
	  /**
	   * Specified LatLng value is used to calculate pixel coordinates and
	   * update the control display. Container is also repositioned.
	   * @param {google.maps.LatLng} latLng Position to display
	   */
	  LatLngControl.prototype.updatePosition = function(latLng) {
	    var projection = this.getProjection();
	    var point = projection.fromLatLngToContainerPixel(latLng);
	    
	    // Update control position to be anchored next to mouse position.
	this.node_.style.left = point.x + this.ANCHOR_OFFSET_.x + 'px';
	this.node_.style.top = point.y + this.ANCHOR_OFFSET_.y + 'px';
	
	// Update control to display latlng and coordinates.
	this.node_.innerHTML = [
	  latLng.toUrlValue(4),
	  '<br/>',
	  point.x,
	  'px, ',
	  point.y,
	  'px'
	].join('');
};