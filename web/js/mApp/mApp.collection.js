var mAppCollection = function( id ) {
	this.id = id;
	
	this.elements = [];
	
	this.init = function() {
	}
	
	this.extend = function( child ) {
		for( var property in this ) {
			if( ('extend' != property && undefined == child[ property ]) || ('toString' == property) ) {
				try {
					child[ property ] = this[ property ];
				} catch( warning ) {
				}
			}
		}
	}
	
	this.add = function( element ) {
		this.elements.push( element );
	}
	
	this.find = function( what ) {
		for( i=0; i<this.elements.length; i++ ) {
			if( this.elements[ i ].id == what ) {
				return this.elements[ i ];
			}
		}
		console.error('mCol:'+ this.id +':find Could not find element '+ what );
		return false;
	}
	
	this.delete = function( elementId ) {
		if( typeof this.find( elementId ).delete == 'function' ) {
			this.find( elementId ).delete();
		}
		
		for( i=0; i<this.elements.length; i++ ) {
			if( this.elements[ i ].id == elementId ) {
				this.elements.splice( i, 1 );
			}
		}
	}
	
	this.getAll = function() {
		return this.elements;
	}
	
	this.getLength = function() {
		return this.elements.length;
	}
		
	this.getId = function() {
		return this.id;
	}
	this.getSelector = function() {
		return '.mCol#' + this.getId();
	}

	this.toArray = function() {
		array = [];
		for( i=0; i<this.elements.length; i++ ) {
			array.push( this.elements[i].toObject() );
		}
		return array;
	}
	
	this.toString = function() {
		return JSON.stringify( this.toArray() );
	}
			
	// RUN INIT
	this.init();
}
