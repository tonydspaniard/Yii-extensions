/*
    geoxml3.js

    Renders KML on the Google Maps JavaScript API Version 3 
    http://code.google.com/p/geoxml3/

   Copyright 2010 Sterling Udell, Larry Ross

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

*/

// Extend the global String object with a method to remove leading and trailing whitespace
if (!String.prototype.trim) {
  String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, '');
  };
}

// Declare namespace
geoXML3 = window.geoXML3 || {instances: []};

// Constructor for the root KML parser object
geoXML3.parser = function (options) {
  // Private variables
  var parserOptions = geoXML3.combineOptions(options, {
    singleInfoWindow: false,
    processStyles: true,
    zoom: true
  });
  var docs = []; // Individual KML documents
  var lastPlacemark;
  var parserName;
  if (!parserOptions.infoWindow && parserOptions.singleInfoWindow)
    parserOptions.infoWindow = new google.maps.InfoWindow();
  // Private methods

  var parse = function (urls, docSet) {
    // Process one or more KML documents
    if (!parserName) {
      parserName = 'geoXML3.instances[' + (geoXML3.instances.push(this) - 1) + ']';
    }
    
    if (typeof urls === 'string') {
      // Single KML document
      urls = [urls];
    }

    // Internal values for the set of documents as a whole
    var internals = {
      parser: this,
      docSet: docSet || [],
      remaining: urls.length,
      parseOnly: !(parserOptions.afterParse || parserOptions.processStyles)
    };
    var thisDoc, j;
    for (var i = 0; i < urls.length; i++) {
      var baseUrl = urls[i].split('?')[0];
      for (j = 0; j < docs.length; j++) {
        if (baseUrl === docs[j].baseUrl) {
          // Reloading an existing document
          thisDoc = docs[j];
          thisDoc.url       = urls[i];
          thisDoc.internals = internals;
          thisDoc.reload    = true;
          docs.splice(j, 1);
          break;
        }
      }
      thisDoc = thisDoc || {
        url:       urls[i], 
        baseUrl:   baseUrl, 
        internals: internals
      };
      internals.docSet.push(thisDoc);
      geoXML3.fetchXML(thisDoc.url, function (responseXML) {render(responseXML, thisDoc);});
    }
  };

  var hideDocument = function (doc) {
    if (!doc) doc = docs[0];
    // Hide the map objects associated with a document 
    var i;
    if (!!doc.markers) {
      for (i = 0; i < doc.markers.length; i++) {
        if(!!doc.markers[i].infoWindow) doc.markers[i].infoWindow.close();
        doc.markers[i].setVisible(false);
      }
    }
    if (!!doc.ggroundoverlays) {
      for (i = 0; i < doc.ggroundoverlays.length; i++) {
      doc.ggroundoverlays[i].setOpacity(0);
      }
    }
    if (!!doc.gpolylines) {
      for (i=0;i<doc.gpolylines.length;i++) {
        doc.gpolylines[i].setMap(null);
      }
    }
    if (!!doc.gpolygons) {
      for (i=0;i<doc.gpolygons.length;i++) {
        doc.gpolygons[i].setMap(null);
      }
    }
  };
  
  var showDocument = function (doc) {
    if (!doc) doc = docs[0];
    // Show the map objects associated with a document 
    var i;
    if (!!doc.markers) {
      for (i = 0; i < doc.markers.length; i++) {
        doc.markers[i].setVisible(true);
      }
    }
    if (!!doc.ggroundoverlays) {
      for (i = 0; i < doc.ggroundoverlays.length; i++) {
        doc.ggroundoverlays[i].setOpacity(doc.ggroundoverlays[i].percentOpacity_);
      }
    }
    if (!!doc.gpolylines) {
      for (i=0;i<doc.gpolylines.length;i++) {
        doc.gpolylines[i].setMap(parserOptions.map);
      }
    }
    if (!!doc.gpolygons) {
      for (i=0;i<doc.gpolygons.length;i++) {
        doc.gpolygons[i].setMap(parserOptions.map);
      }
    }
  };

function processStyle(thisNode, styles, styleID) {
      var nodeValue  = geoXML3.nodeValue;
      var defaultStyle = {
        color: "ff000000", // black
        width: 1,
        fill: true,
        outline: true,
        fillcolor: "3fff0000" // blue
      };
      styles[styleID] = styles[styleID] || defaultStyle;
      var styleNodes = thisNode.getElementsByTagName('Icon');
      if (!!styleNodes && !!styleNodes.length && (styleNodes.length > 0)) {
        styles[styleID] = {
          href: nodeValue(styleNodes[0].getElementsByTagName('href')[0]),
          scale: nodeValue(styleNodes[0].getElementsByTagName('scale')[0])
        };
        if (!isNaN(styles[styleID].scale)) styles[styleID].scale = 1.0;
      }
      styleNodes = thisNode.getElementsByTagName('LineStyle');
      if (!!styleNodes && !!styleNodes.length && (styleNodes.length > 0)) {
        styles[styleID].color = nodeValue(styleNodes[0].getElementsByTagName('color')[0]),
        styles[styleID].width = nodeValue(styleNodes[0].getElementsByTagName('width')[0])
      }
      styleNodes = thisNode.getElementsByTagName('PolyStyle');
      if (!!styleNodes && !!styleNodes.length && (styleNodes.length > 0)) {
        styles[styleID].outline   = getBooleanValue(styleNodes[0].getElementsByTagName('outline')[0]);
        styles[styleID].fill      = getBooleanValue(styleNodes[0].getElementsByTagName('fill')[0]);
        styles[styleID].fillcolor = nodeValue(styleNodes[0].getElementsByTagName('color')[0]);
      }
      return styles[styleID];
}

function getBooleanValue(node) {
  var nodeContents = geoXML3.nodeValue(node);
  if (!nodeContents) return true;
  if (nodeContents) nodeContents = parseInt(nodeContents);
  if (isNaN(nodeContents)) return true;
  if (nodeContents == 0) return false;
  else return true;
}   

function processPlacemarkCoords(node, tag) {
   var parent = node.getElementsByTagName(tag);
var coordListA = [];
  for (var i=0; i<parent.length; i++) {
  var coordNodes = parent[i].getElementsByTagName('coordinates')
  if (!coordNodes) {
    if (coordListA.length > 0) {
      break;
    } else {
      return [{coordinates: []}];
    }
  }

  for (var j=0; j<coordNodes.length;j++) { 
    var coords = geoXML3.nodeValue(coordNodes[j]).trim();
    coords = coords.replace(/,\s+/g, ',');
    var path = coords.split(/\s+/g);
    var pathLength = path.length;
    var coordList = [];
    for (var k = 0; k < pathLength; k++) {
      coords = path[k].split(',');
      coordList.push({
        lat: parseFloat(coords[1]), 
        lng: parseFloat(coords[0]), 
        alt: parseFloat(coords[2])
      });
    }
    coordListA.push({coordinates: coordList});
  }
}
  return coordListA;
}

  var render = function (responseXML, doc) {
    // Callback for retrieving a KML document: parse the KML and display it on the map
    if (!responseXML) {
      // Error retrieving the data
      geoXML3.log('Unable to retrieve ' + doc.url);
      if (parserOptions.failedParse) {
        parserOptions.failedParse(doc);
      }
    } else if (!doc) {
      throw 'geoXML3 internal error: render called with null document';
    } else { //no errors
      var i;
      var styles = {};
      doc.placemarks     = [];
      doc.groundoverlays = [];
      doc.ggroundoverlays = [];
      doc.networkLinks   = [];
      doc.gpolygons      = [];
      doc.gpolylines     = [];

    // Declare some helper functions in local scope for better performance
    var nodeValue  = geoXML3.nodeValue;

    // Parse styles
    var styleID, styleNodes;
    nodes = responseXML.getElementsByTagName('Style');
    nodeCount = nodes.length;
    for (i = 0; i < nodeCount; i++) {
      thisNode = nodes[i];
      var thisNodeId = thisNode.getAttribute('id');
      if (!!thisNodeId) {
        styleID    = '#' + thisNodeId;
        processStyle(thisNode, styles, styleID);
      }
    }
    doc.styles = styles;
      if (!!parserOptions.processStyles || !parserOptions.createMarker) {
        // Convert parsed styles into GMaps equivalents
        processStyles(doc);
      }
      
      // Parse placemarks
      if (!!doc.reload && !!doc.markers) {
        for (i = 0; i < doc.markers.length; i++) {
          doc.markers[i].active = false;
        }
      }
      var placemark, node, coords, path, marker, poly;
      var placemark, coords, path, pathLength, marker, polygonNodes, coordList;
      var placemarkNodes = responseXML.getElementsByTagName('Placemark');
      for (pm = 0; pm < placemarkNodes.length; pm++) {
        // Init the placemark object
        node = placemarkNodes[pm];
        placemark = {
          name:  geoXML3.nodeValue(node.getElementsByTagName('name')[0]),
          description: geoXML3.nodeValue(node.getElementsByTagName('description')[0]),
          styleUrl: geoXML3.nodeValue(node.getElementsByTagName('styleUrl')[0])
        };
        var defaultStyle = {
          color: "ff000000", // black
          width: 1,
          fill: true,
          outline: true,
          fillcolor: "3fff0000" // blue
        };
        placemark.style = doc.styles[placemark.styleUrl] || defaultStyle;
        // inline style overrides shared style
        var inlineStyles = node.getElementsByTagName('Style');
        if (inlineStyles && (inlineStyles.length > 0)) {
          var style = processStyle(node,doc.styles,"inline");
	  processStyleID(style);
	  if (style) placemark.style = style;
        }
        if (/^https?:\/\//.test(placemark.description)) {
          placemark.description = ['<a href="', placemark.description, '">', placemark.description, '</a>'].join('');
        }

        // process MultiGeometry
        var GeometryNodes = node.getElementsByTagName('coordinates');
        var Geometry = null;
	if (!!GeometryNodes && (GeometryNodes.length > 0)) {
          for (var gn=0;gn<GeometryNodes.length;gn++) {
             if (!GeometryNodes[gn].parentNode ||
                 !GeometryNodes[gn].parentNode.nodeName) {

             } else { // parentNode.nodeName exists
               var GeometryPN = GeometryNodes[gn].parentNode;
               Geometry = GeometryPN.nodeName;
       
        // Extract the coordinates
        // What sort of placemark?
        switch(Geometry) {
          case "Point":
            placemark.Point = processPlacemarkCoords(node, "Point")[0]; 
            placemark.latlng = new google.maps.LatLng(placemark.Point.coordinates[0].lat, placemark.Point.coordinates[0].lng);
            pathLength = 1;
            break;
          case "LinearRing":
            // Polygon/line
            polygonNodes = node.getElementsByTagName('Polygon');
            // Polygon
            if (!placemark.Polygon)
              placemark.Polygon = [{
                outerBoundaryIs: {coordinates: []},
                innerBoundaryIs: [{coordinates: []}]
              }];
            for (var pg=0;pg<polygonNodes.length;pg++) {
               placemark.Polygon[pg] = {
                 outerBoundaryIs: {coordinates: []},
                 innerBoundaryIs: [{coordinates: []}]
               }
               placemark.Polygon[pg].outerBoundaryIs = processPlacemarkCoords(polygonNodes[pg], "outerBoundaryIs");
               placemark.Polygon[pg].innerBoundaryIs = processPlacemarkCoords(polygonNodes[pg], "innerBoundaryIs");
            }
            coordList = placemark.Polygon[0].outerBoundaryIs;
            break;

          case "LineString":
            pathLength = 0;
            placemark.LineString = processPlacemarkCoords(node,"LineString");
            break;

          default:
            break;
      }
      } // parentNode.nodeName exists
      } // GeometryNodes loop
      } // if GeometryNodes 
      // call the custom placemark parse function if it is defined
      if (!!parserOptions.pmParseFn) parserOptions.pmParseFn(node, placemark);
      doc.placemarks.push(placemark);
      
      if (placemark.Point) {
          if (parserOptions.zoom && !!google.maps) {
            doc.bounds = doc.bounds || new google.maps.LatLngBounds();
            doc.bounds.extend(placemark.latlng);
          }

          if (!!parserOptions.createMarker) {
            // User-defined marker handler
            parserOptions.createMarker(placemark, doc);
          } else { // !user defined createMarker
            // Check to see if this marker was created on a previous load of this document
            var found = false;
            if (!!doc) {
              doc.markers = doc.markers || [];
              if (doc.reload) {
                for (var j = 0; j < doc.markers.length; j++) {
                  if (doc.markers[j].getPosition().equals(placemark.latlng)) {
                    found = doc.markers[j].active = true;
                    break;
                  }
                }
              } 
            }

            if (!found) {
              // Call the built-in marker creator
              marker = createMarker(placemark, doc);
              marker.active = true;
            }
          }
        }
        if (placemark.Polygon) { // poly test 2
            if (!!doc) {
              doc.gpolygons = doc.gpolygons || [];
            }

            if (!!parserOptions.createPolygon) {
              // User-defined polygon handler
              poly = parserOptions.createPolygon(placemark, doc);
            } else {  // ! user defined createPolygon
              // Check to see if this marker was created on a previous load of this document
              poly = createPolygon(placemark,doc);
              poly.active = true;
            }
          if (parserOptions.zoom && !!google.maps) {
            doc.bounds = doc.bounds || new google.maps.LatLngBounds();
            doc.bounds.union(poly.bounds);
          }
          } 
          if (placemark.LineString) { // polyline
            if (!!doc) {
              doc.gpolylines = doc.gpolylines || [];
            }
            if (!!parserOptions.createPolyline) {
              // User-defined polyline handler
              poly = parserOptions.createPolyline(placemark, doc);
            } else { // ! user defined createPolyline
              // Check to see if this marker was created on a previous load of this document
              poly = createPolyline(placemark,doc);
              poly.active = true;
            }
          if (parserOptions.zoom && !!google.maps) {
            doc.bounds = doc.bounds || new google.maps.LatLngBounds();
            doc.bounds.union(poly.bounds);
          }
          }
          
      } // placemark loop

      if (!!doc.reload && !!doc.markers) {
        for (i = doc.markers.length - 1; i >= 0 ; i--) {
          if (!doc.markers[i].active) {
            if (!!doc.markers[i].infoWindow) {
              doc.markers[i].infoWindow.close();
            }
            doc.markers[i].setMap(null);
            doc.markers.splice(i, 1);
          }
        }
      }

      // Parse ground overlays
      if (!!doc.reload && !!doc.groundoverlays) {
        for (i = 0; i < doc.groundoverlays.length; i++) {
          doc.groundoverlays[i].active = false;
        }
      }

      if (!!doc) {
        doc.groundoverlays = doc.groundoverlays || [];
      }
      // doc.groundoverlays =[];
      var groundOverlay, color, transparency, overlay;
      var groundNodes = responseXML.getElementsByTagName('GroundOverlay');
      for (i = 0; i < groundNodes.length; i++) {
        node = groundNodes[i];
        
        // Init the ground overlay object
        groundOverlay = {
          name:        geoXML3.nodeValue(node.getElementsByTagName('name')[0]),
          description: geoXML3.nodeValue(node.getElementsByTagName('description')[0]),
          icon: {href: geoXML3.nodeValue(node.getElementsByTagName('href')[0])},
          latLonBox: {
            north: parseFloat(geoXML3.nodeValue(node.getElementsByTagName('north')[0])),
            east:  parseFloat(geoXML3.nodeValue(node.getElementsByTagName('east')[0])),
            south: parseFloat(geoXML3.nodeValue(node.getElementsByTagName('south')[0])),
            west:  parseFloat(geoXML3.nodeValue(node.getElementsByTagName('west')[0]))
          }
        };
        if (parserOptions.zoom && !!google.maps) {
          doc.bounds = doc.bounds || new google.maps.LatLngBounds();
          doc.bounds.union(new google.maps.LatLngBounds(
            new google.maps.LatLng(groundOverlay.latLonBox.south, groundOverlay.latLonBox.west),
            new google.maps.LatLng(groundOverlay.latLonBox.north, groundOverlay.latLonBox.east)
          ));
        }

      // Opacity is encoded in the color node
      var colorNode = node.getElementsByTagName('color');
      if ( colorNode && colorNode.length && (colorNode.length > 0)) {
        groundOverlay.opacity = geoXML3.getOpacity(nodeValue(colorNode[0]));
      } else {
        groundOverlay.opacity = 0.45;
      }

      doc.groundoverlays.push(groundOverlay);
  
        if (!!parserOptions.createOverlay) {
          // User-defined overlay handler
          parserOptions.createOverlay(groundOverlay, doc);
        } else { // ! user defined createOverlay
          // Check to see if this overlay was created on a previous load of this document
          var found = false;
          if (!!doc) {
            doc.groundoverlays = doc.groundoverlays || [];
            if (doc.reload) {
              overlayBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(groundOverlay.latLonBox.south, groundOverlay.latLonBox.west),
                new google.maps.LatLng(groundOverlay.latLonBox.north, groundOverlay.latLonBox.east));
            var overlays = doc.groundoverlays;
            for (i = overlays.length; i--;) {
              if ((overlays[i].bounds().equals(overlayBounds)) &&
                  (overlays.url_ === groundOverlay.icon.href)) {
                found = overlays[i].active = true;
                break;
              }
            }
          } 
        }
  
          if (!found) {
            // Call the built-in overlay creator
            overlay = createOverlay(groundOverlay, doc);
            overlay.active = true;
          }
        }
    if (!!doc.reload && !!doc.groundoverlays && !!doc.groundoverlays.length) {
      var overlays = doc.groundoverlays;
      for (i = overlays.length; i--;) {
        if (!overlays[i].active) {
          overlays[i].remove();
          overlays.splice(i, 1);
          }
      }
      doc.groundoverlays = overlays;
    }
      }
      // Parse network links
      var networkLink;
      var docPath = document.location.pathname.split('/');
      docPath = docPath.splice(0, docPath.length - 1).join('/');
      var linkNodes = responseXML.getElementsByTagName('NetworkLink');
      for (i = 0; i < linkNodes.length; i++) {
        node = linkNodes[i];
        
        // Init the network link object
        networkLink = {
          name: geoXML3.nodeValue(node.getElementsByTagName('name')[0]),
          link: {
            href:        geoXML3.nodeValue(node.getElementsByTagName('href')[0]),
            refreshMode:     geoXML3.nodeValue(node.getElementsByTagName('refreshMode')[0])
          }
        };
        
        // Establish the specific refresh mode 
        if (networkLink.link.refreshMode === '') {
          networkLink.link.refreshMode = 'onChange';
        }
        if (networkLink.link.refreshMode === 'onInterval') {
          networkLink.link.refreshInterval = parseFloat(geoXML3.nodeValue(node.getElementsByTagName('refreshInterval')[0]));
          if (isNaN(networkLink.link.refreshInterval)) {
            networkLink.link.refreshInterval = 0;
          }
        } else if (networkLink.link.refreshMode === 'onChange') {
          networkLink.link.viewRefreshMode = geoXML3.nodeValue(node.getElementsByTagName('viewRefreshMode')[0]);
          if (networkLink.link.viewRefreshMode === '') {
            networkLink.link.viewRefreshMode = 'never';
          }
          if (networkLink.link.viewRefreshMode === 'onStop') {
            networkLink.link.viewRefreshTime = geoXML3.nodeValue(node.getElementsByTagName('refreshMode')[0]);
            networkLink.link.viewFormat =      geoXML3.nodeValue(node.getElementsByTagName('refreshMode')[0]);
            if (networkLink.link.viewFormat === '') {
              networkLink.link.viewFormat = 'BBOX=[bboxWest],[bboxSouth],[bboxEast],[bboxNorth]';
            }
          }
        }

        if (!/^[\/|http]/.test(networkLink.link.href)) {
          // Fully-qualify the HREF
          networkLink.link.href = docPath + '/' + networkLink.link.href;
        }

        // Apply the link
        if ((networkLink.link.refreshMode === 'onInterval') && 
            (networkLink.link.refreshInterval > 0)) {
          // Reload at regular intervals
          setInterval(parserName + '.parse("' + networkLink.link.href + '")', 
                      1000 * networkLink.link.refreshInterval); 
        } else if (networkLink.link.refreshMode === 'onChange') {
          if (networkLink.link.viewRefreshMode === 'never') {
            // Load the link just once
            doc.internals.parser.parse(networkLink.link.href, doc.internals.docSet);
          } else if (networkLink.link.viewRefreshMode === 'onStop') {
            // Reload when the map view changes
            
          }
        }
      }
}

      if (!!doc.bounds) {
        doc.internals.bounds = doc.internals.bounds || new google.maps.LatLngBounds();
        doc.internals.bounds.union(doc.bounds); 
      }
      if (!!doc.markers || !!doc.groundoverlays || !!doc.gpolylines || !!doc.gpolygons) {
        doc.internals.parseOnly = false;
      }

      doc.internals.remaining -= 1;
      if (doc.internals.remaining === 0) {
        // We're done processing this set of KML documents
        // Options that get invoked after parsing completes
        if (!!doc.internals.bounds) {
          parserOptions.map.fitBounds(doc.internals.bounds); 
        }
        if (parserOptions.afterParse) {
          parserOptions.afterParse(doc.internals.docSet);
        }

        if (!doc.internals.parseOnly) {
          // geoXML3 is not being used only as a real-time parser, so keep the processed documents around
            for (var i=(doc.internals.docSet.length-1);i>=0;i--) {
              docs.push(doc.internals.docSet[i]);
            }
        }
      }
  };

var kmlColor = function (kmlIn) {
  var kmlColor = {};
  if (kmlIn) {
   aa = kmlIn.substr(0,2);
   bb = kmlIn.substr(2,2);
   gg = kmlIn.substr(4,2);
   rr = kmlIn.substr(6,2);
   kmlColor.color = "#" + rr + gg + bb;
   kmlColor.opacity = parseInt(aa,16)/256;
  } else {
   // defaults
   kmlColor.color = randomColor();
   kmlColor.opacity = 0.45;
  }
  return kmlColor;
}

var randomColor = function(){ 
  var color="#";
  var colorNum = Math.random()*8388607.0;  // 8388607 = Math.pow(2,23)-1
  var colorStr = colorNum.toString(16);
  color += colorStr.substring(0,colorStr.indexOf('.'));
  return color;
};

  var processStyleID = function (style) {
      var zeroPoint = new google.maps.Point(0,0);
      if (!!style.href) {
        var markerRegEx = /\/(red|blue|green|yellow|lightblue|purple|pink|orange|pause|go|stop)(-dot)?\.png/;
        if (markerRegEx.test(style.href)) {
         //bottom middle
	  var anchorPoint = new google.maps.Point(16*style.scale, 32*style.scale);
	} else {
	  var anchorPoint = new google.maps.Point(16*style.scale, 12*style.scale);
	}
        // Init the style object with a standard KML icon
        style.icon =  new google.maps.MarkerImage(
          style.href,
          new google.maps.Size(32*style.scale, 32*style.scale),
          zeroPoint,
          // bottom middle 
          anchorPoint,
          new google.maps.Size(32,32)

        );

        // Look for a predictable shadow
        var stdRegEx = /\/(red|blue|green|yellow|lightblue|purple|pink|orange)(-dot)?\.png/;
        var shadowSize = new google.maps.Size(59, 32);
	var shadowPoint = new google.maps.Point(16,32);
        if (stdRegEx.test(style.href)) {
          // A standard GMap-style marker icon
          style.shadow = new google.maps.MarkerImage(
              'http://maps.google.com/mapfiles/ms/micons/msmarker.shadow.png',
              shadowSize,
              zeroPoint,
              shadowPoint);
        } else if (style.href.indexOf('-pushpin.png') > -1) {
          // Pushpin marker icon
          style.shadow = new google.maps.MarkerImage(
            'http://maps.google.com/mapfiles/ms/micons/pushpin_shadow.png',
            shadowSize,
            zeroPoint,
            shadowPoint);
        } else {
          // Other MyMaps KML standard icon
          style.shadow = new google.maps.MarkerImage(
            style.href.replace('.png', '.shadow.png'),
            shadowSize,
            zeroPoint,
            shadowPoint);
        }
      }
  }
    
  var processStyles = function (doc) {
    for (var styleID in doc.styles) {
      processStyleID(doc.styles[styleID]);
    }
  };

  var createMarker = function (placemark, doc) {
    // create a Marker to the map from a placemark KML object

    // Load basic marker properties
    var markerOptions = geoXML3.combineOptions(parserOptions.markerOptions, {
      map:      parserOptions.map,
      position: new google.maps.LatLng(placemark.Point.coordinates[0].lat, placemark.Point.coordinates[0].lng),
      title:    placemark.name,
      zIndex:   Math.round(placemark.Point.coordinates[0].lat * -100000)<<5,
      icon:     placemark.style.icon,
      shadow:   placemark.style.shadow 
    });
  
    // Create the marker on the map
    var marker = new google.maps.Marker(markerOptions);
    if (!!doc) {
      doc.markers.push(marker);
    }

    // Set up and create the infowindow
    var infoWindowOptions = geoXML3.combineOptions(parserOptions.infoWindowOptions, {
      content: '<div class="geoxml3_infowindow"><h3>' + placemark.name + 
               '</h3><div>' + placemark.description + '</div></div>',
      pixelOffset: new google.maps.Size(0, 2)
    });
    if (parserOptions.infoWindow) {
      marker.infoWindow = parserOptions.infoWindow;
    } else {
      marker.infoWindow = new google.maps.InfoWindow(infoWindowOptions);
    }
    // Infowindow-opening event handler
    google.maps.event.addListener(marker, 'click', function() {
      marker.infoWindow.setOptions(infoWindowOptions);
      this.infoWindow.open(this.map, this);
    });
    placemark.marker = marker;
    return marker;
  };
  
  var createOverlay = function (groundOverlay, doc) {
    // Add a ProjectedOverlay to the map from a groundOverlay KML object

    if (!window.ProjectedOverlay) {
      throw 'geoXML3 error: ProjectedOverlay not found while rendering GroundOverlay from KML';
    }

    var bounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(groundOverlay.latLonBox.south, groundOverlay.latLonBox.west),
        new google.maps.LatLng(groundOverlay.latLonBox.north, groundOverlay.latLonBox.east)
    );
    var overlayOptions = geoXML3.combineOptions(parserOptions.overlayOptions, {percentOpacity: groundOverlay.opacity*100});
    var overlay = new ProjectedOverlay(parserOptions.map, groundOverlay.icon.href, bounds, overlayOptions);
    
    if (!!doc) {
      doc.ggroundoverlays = doc.ggroundoverlays || [];
      doc.ggroundoverlays.push(overlay);
    }

    return overlay;
  };

// Create Polyline

  var createPolyline = function(placemark, doc) {
    var path = [];
    for (var j=0; j<placemark.LineString.length; j++) {
      var coords = placemark.LineString[j].coordinates;
      var bounds = new google.maps.LatLngBounds();
      for (var i=0;i<coords.length;i++) {
        var pt = new google.maps.LatLng(coords[i].lat, coords[i].lng);
        path.push(pt);
        bounds.extend(pt);
      }
    }
      // point to open the infowindow if triggered 
      var point = path[Math.floor(path.length/2)];
      // Load basic polyline properties
      var kmlStrokeColor = kmlColor(placemark.style.color);
      var polyOptions = geoXML3.combineOptions(parserOptions.polylineOptions, {
        map:      parserOptions.map,
        path: path,
        strokeColor: kmlStrokeColor.color,
        strokeWeight: placemark.style.width,
        strokeOpacity: kmlStrokeColor.opacity,
        title:    placemark.name
      });
      var infoWindowOptions = geoXML3.combineOptions(parserOptions.infoWindowOptions, {
        content: '<div class="geoxml3_infowindow"><h3>' + placemark.name + 
                 '</h3><div>' + placemark.description + '</div></div>',
        pixelOffset: new google.maps.Size(0, 2)
      });
    var p = new google.maps.Polyline(polyOptions);
    p.bounds = bounds;
    if (parserOptions.infoWindow) {
      p.infoWindow = parserOptions.infoWindow;
    } else {
      p.infoWindow = new google.maps.InfoWindow(infoWindowOptions);
    }
    // Infowindow-opening event handler
    google.maps.event.addListener(p, 'click', function(e) {
      p.infoWindow.setOptions(infoWindowOptions);
      if (e && e.latLng) {
        p.infoWindow.setPosition(e.latLng);
      } else {
        p.infoWindow.setPosition(point);
      }
      p.infoWindow.open(this.map);
    });
    if (!!doc) doc.gpolylines.push(p);
  placemark.polyline = p;
  return p;
}

// Create Polygon

var createPolygon = function(placemark, doc) {
  var bounds = new google.maps.LatLngBounds();
  var pathsLength = 0;
    var paths = [];
    for (var polygonPart=0;polygonPart<placemark.Polygon.length;polygonPart++) {
    for (var j=0; j<placemark.Polygon[polygonPart].outerBoundaryIs.length; j++) {
      var coords = placemark.Polygon[polygonPart].outerBoundaryIs[j].coordinates;
      var path = [];
      for (var i=0;i<coords.length;i++) {
        var pt = new google.maps.LatLng(coords[i].lat, coords[i].lng);
        path.push(pt);
        bounds.extend(pt);
      }
      paths.push(path);
      pathsLength += path.length;
    }
    for (var j=0; j<placemark.Polygon[polygonPart].innerBoundaryIs.length; j++) {
      var coords = placemark.Polygon[polygonPart].innerBoundaryIs[j].coordinates;
      var path = [];
      for (var i=0;i<coords.length;i++) {
        var pt = new google.maps.LatLng(coords[i].lat, coords[i].lng);
        path.push(pt);
        bounds.extend(pt);
      }
      paths.push(path);
      pathsLength += path.length;
    }
  }

      // Load basic polygon properties
      var kmlStrokeColor = kmlColor(placemark.style.color);
      var kmlFillColor = kmlColor(placemark.style.fillcolor);
      if (!placemark.style.fill) kmlFillColor.opacity = 0.0;
      var strokeWeight = placemark.style.width;
      if (!placemark.style.outline) {
        strokeWeight = 0;
	kmlStrokeColor.opacity = 0.0;
      }
      var polyOptions = geoXML3.combineOptions(parserOptions.polygonOptions, {
        map:      parserOptions.map,
        paths:    paths,
        title:    placemark.name,
        strokeColor: kmlStrokeColor.color,
        strokeWeight: strokeWeight,
        strokeOpacity: kmlStrokeColor.opacity,
        fillColor: kmlFillColor.color,
        fillOpacity: kmlFillColor.opacity
      });

      var infoWindowOptions = geoXML3.combineOptions(parserOptions.infoWindowOptions, {
        content: '<div class="geoxml3_infowindow"><h3>' + placemark.name + 
                 '</h3><div>' + placemark.description + '</div></div>',
        pixelOffset: new google.maps.Size(0, 2)
      });
    var p = new google.maps.Polygon(polyOptions);
    p.bounds = bounds;
    if (parserOptions.infoWindow) {
      p.infoWindow = parserOptions.infoWindow;
    } else {
      p.infoWindow = new google.maps.InfoWindow(infoWindowOptions);
    }
    // Infowindow-opening event handler
    google.maps.event.addListener(p, 'click', function(e) {
      p.infoWindow.setOptions(infoWindowOptions);
      if (e && e.latLng) {
        p.infoWindow.setPosition(e.latLng);
      } else {
        p.infoWindow.setPosition(p.bounds.getCenter());
      }
      p.infoWindow.open(this.map);
    });
    if (!!doc) doc.gpolygons.push(p);
  placemark.polygon = p;
  return p;
}

  return {
    // Expose some properties and methods

    options: parserOptions,
    docs:    docs,
    
    parse:          parse,
    hideDocument:   hideDocument,
    showDocument:   showDocument,
    processStyles:  processStyles, 
    createMarker:   createMarker,
    createOverlay:  createOverlay,
    createPolyline: createPolyline,
    createPolygon:  createPolygon
  };
};
// End of KML Parser

// Helper objects and functions
geoXML3.getOpacity = function (kmlColor) {
  // Extract opacity encoded in a KML color value. Returns a number between 0 and 1.
  if (!!kmlColor &&
      (kmlColor !== '') &&
      (kmlColor.length == 8)) {
    var transparency = parseInt(kmlColor.substr(0, 2), 16);
    return transparency / 255;
  } else {
    return 1;
  }
};

// Log a message to the debugging console, if one exists
geoXML3.log = function(msg) {
  if (!!window.console) {
    console.log(msg);
  } else { alert("log:"+msg); }
};

// Combine two options objects: a set of default values and a set of override values 
geoXML3.combineOptions = function (overrides, defaults) {
  var result = {};
  if (!!overrides) {
    for (var prop in overrides) {
      if (overrides.hasOwnProperty(prop)) {
        result[prop] = overrides[prop];
      }
    }
  }
  if (!!defaults) {
    for (prop in defaults) {
      if (defaults.hasOwnProperty(prop) && (result[prop] === undefined)) {
        result[prop] = defaults[prop];
      }
    }
  }
  return result;
};

// Retrieve an XML document from url and pass it to callback as a DOM document
geoXML3.fetchers = [];

// parse text to XML doc
/**
 * Parses the given XML string and returns the parsed document in a
 * DOM data structure. This function will return an empty DOM node if
 * XML parsing is not supported in this browser.
 * @param {string} str XML string.
 * @return {Element|Document} DOM.
 */
geoXML3.xmlParse = function (str) {
  if (typeof ActiveXObject != 'undefined' && typeof GetObject != 'undefined') {
    var doc = new ActiveXObject('Microsoft.XMLDOM');
    doc.loadXML(str);
    return doc;
  }

  if (typeof DOMParser != 'undefined') {
    return (new DOMParser()).parseFromString(str, 'text/xml');
  }

  return createElement('div', null);
}

geoXML3.fetchXML = function (url, callback) {
  function timeoutHandler() {
    callback();
  };

  var xhrFetcher;
  if (!!geoXML3.fetchers.length) {
    xhrFetcher = geoXML3.fetchers.pop();
  } else {
    if (!!window.XMLHttpRequest) {
      xhrFetcher = new window.XMLHttpRequest(); // Most browsers
    } else if (!!window.ActiveXObject) {
      xhrFetcher = new window.ActiveXObject('Microsoft.XMLHTTP'); // Some IE
    }
  }

  if (!xhrFetcher) {
    geoXML3.log('Unable to create XHR object');
    callback(null);
  } else {
    xhrFetcher.open('GET', url, true);
    xhrFetcher.onreadystatechange = function () {
      if (xhrFetcher.readyState === 4) {
        // Retrieval complete
        if (!!geoXML3.xhrtimeout)
          clearTimeout(geoXML3.xhrtimeout);
        if (xhrFetcher.status >= 400) {
          geoXML3.log('HTTP error ' + xhrFetcher.status + ' retrieving ' + url);
          callback();
        } else {
          // Returned successfully
	    callback(geoXML3.xmlParse(xhrFetcher.responseText));
        }
        // We're done with this fetcher object
        geoXML3.fetchers.push(xhrFetcher);
      }
    };
    geoXML3.xhrtimeout = setTimeout(timeoutHandler, 60000);
    xhrFetcher.send(null);
  }
};

//nodeValue: Extract the text value of a DOM node, with leading and trailing whitespace trimmed
geoXML3.nodeValue = function(node) {
  var retStr="";
  if (!node) {
    return '';
  }
   if(node.nodeType==3||node.nodeType==4||node.nodeType==2){
      retStr+=node.nodeValue;
   }else if(node.nodeType==1||node.nodeType==9||node.nodeType==11){
      for(var i=0;i<node.childNodes.length;++i){
         retStr+=arguments.callee(node.childNodes[i]);
      }
   }
   return retStr;
};