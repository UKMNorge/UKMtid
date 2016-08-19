var mViewModel = function( id ) {
	this.id = id;
	
	this.getId = function() {
		return this.id;
	}
	
	this.getSelector = function() {
		return '.mView#' + this.getId();
	}
	
	this.init = function() {
		console.log('VIEW:init '+ this.getId() );
		this._hasContainer();
	}
	
	this.render = function( ) {
		console.info('VIEW:render ' + this.getId());
		this._hasContainer();
	}
	
	this._hasContainer = function() {
		if( 'test' != this.getId() && null == document.getElementById( this.getId() ) ) {
			console.error('VIEW '+ this.getId() +' does not have a view container and cannot be selected!');
			return false;
		}
		return true;
	}
	
	this.extend = function( child ) {
		for( var property in this ) {
			if( property != 'extend' && undefined == child[ property ] ) {
				try {
					child[ property ] = this[ property ];
				} catch( warning ) {
				}
			}
		}
	}
	
	this.registerHooks = function() {
	}
	
	this.trigger = function( trigger, data=undefined ) {
		trigger = this.getId() +':' + trigger;
		console.warn( 'TRIGGER: ' + trigger );
		$(document).trigger( trigger );
	}
	
	// RUN INIT
	this.init();
}
