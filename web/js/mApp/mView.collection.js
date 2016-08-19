var mViewCollection = function( viewContainer ) {
	this.id = viewContainer;
	
	this.views = [];
	this.default = 1;
	this.active = null;
	
	this.init = function() {
		console.info('VIEWcoll:init');
	}
	
	this.add = function( view ) {
		console.info('VIEWcoll:add: "'+ view.getId() +'"');
		this.views.push( view );
	}
	
	this.find = function( what ) {
		console.info('VIEWcoll:find '+ what );
		for( i=0; i<this.views.length; i++ ) {
			if( this.views[ i ].id == what ) {
				console.log('VIEWcoll:find Found '+ what +' @ index:' + i );
				return this.views[ i ];
			}
		}
		console.error('VIEWcoll:find Could not find view '+ what );
		return this.views[ this._getDefault() ];
	}
	
	this.create = function( id, object ) {
		console.info('VIEWcoll:create '+ id );
		if( undefined !== object ) {
			// validate
			try {
				test = new mViewModel('test');
				console.group('Props');
				for( prop in test ) {
					// if is function - check if exists
					console.log( prop );
				}
				console.groupEnd();
				// TMP-check
				object.getId();
				object.getSelector();
			} catch( warning ) {
				console.error( 'VIEWcoll:create "'+ id +'": Invalid viewModel given! ' + "\r\n" + warning );
			}
			view = object;
		} else {
			view = new mViewModel( id );	
		}
		this.add( view );
		view.registerHooks();
		return view;
	}
	
	this.render = function( view_id ) {
		console.info('VIEWcoll:render ' + view_id );
		exiting = this._getActive();

		this._setActive( view_id );

		this.trigger('preRender');
		this.trigger('preRender:'+ this._getActive().getId() );
		
		this._getActive().render();
		
		if( null == exiting ) {
			console.log('EXITING null');
			this._show( exiting );
		} else if ( null != exiting && exiting.getId() == view_id ) {
			console.log('EXITING '+ exiting.getId() +' for '+ view_id +' (SESAME)');
		} else {
			console.log('EXITING '+ exiting.getId() +' for '+ view_id);
			this._show( exiting );
		}
		this.trigger('postRender:'+ this._getActive().getId() );
		this.trigger('postRender');
	}
	
	this.getId = function() {
		return this.id;
	}
	this.getSelector = function() {
		return '.mViews#' + this.getId();
	}
		
	this._setActive = function( view ) {
		console.info('VIEWcoll:setActive "'+ view + '"');
		this.active = this.find( view );
		if( view == this.active.getId() ) {
			console.log('VIEWcoll:setActive Requested view "'+ view +'" exists, and is now set as active.');
		} else {
			console.error('VIEWcoll:setActive Requested view not found, rendering defaultView.');
		}
	}
	this._getActive = function() {
		return this.active;
	}
	
	this._setDefault = function( array_position ) {
		this.default = array_position;
	}
	this._getDefault = function() {
		return this.default;
	}
	
	this._show = function( initial ) {
		console.info('VIEWcoll:show');
		if( null == initial ) {
			// HIDE ALL
			$( '#'+ this.getId() +' > div' ).hide();
			// SHOW SELECTED VEIW
			$( '#'+ this.getId() +' ' + this._getActive().getSelector() ).fadeIn(300);
		} else {
			// HIDE ALL
			$( '#'+ this.getId() +' > div' ).slideUp();
			// SHOW SELECTED VEIW
			$( '#'+ this.getId() +' ' + this._getActive().getSelector() ).fadeIn();
		}
	}
	
	this.trigger = function( trigger, data=undefined ) {
		trigger = this.getId() +':' + trigger;
		console.warn( 'TRIGGER: ' + trigger );
		$(document).trigger( trigger );
	}
	
	// RUN INIT
	this.init();
}
