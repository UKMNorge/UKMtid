var mMessage = function( id ) {
	this.id = null;
	this.containerId = null;
	this.messageContainerId = null;
	
	this.message = null;
	this.level = null;
	
	this.init = function( id ) {
		this.setId( id );
		this.containerId 		= '#'+ id +'Container';
		this.messageContainerId = '#'+ id;
		this._registerListeners();
	}
	
	this.show = function( alertLevel, message, body ) {
		if( undefined == body ) {
			this.message = message;
		} else {
			this.message = '<b>'+ message +'</b> '+ body;
		}
		this.level = alertLevel;
		
		this.render();
		return this;
	}
	
	this.render = function() {
		if( !this._getMessageContainer().hasClass( 'alert-'+this.getLevel() ) ) {
			this._getMessageContainer().removeClass('alert-success alert-info alert-warning alert-danger');
			if( this._getMessageContainer().is(':visible') ) {
				this._getMessageContainer().addClass( 'alert-'+ this.getLevel() ).effect('highlight', {color: this._getHighlightColor() } );
			} else {
				this._getMessageContainer().addClass( 'alert-'+ this.getLevel() );	
			}
		}
		this._getMessageContainer().html( this.getMessage() );
		this._getContainer().fadeIn();
		return this;
	}
	
	this.hideNow = function() {
		this._getContainer().slideUp();
		return this;
	}
	
	this.hideAfter = function( seconds ) {
		setTimeout( function( id, mMessageObject ){
						$(document).trigger('mMessage:'+ id +':hide', mMessageObject);
					}, (seconds*1000), this.getId(), this );
		return this;
	}
	
	/**
	 * getMessage
	 * @return string
	**/
	this.getMessage = function() {
		return this.message;
	}
	/**
	 * setMessage
	 * @param string $message
	 * @return this
	**/
	this.setMessage = function( message ) {
		this.message = message;
		return this;
	}
	
	/**
	 * getLevel
	 * @return string
	**/
	this.getLevel = function() {
		return this.level;
	}
	/**
	 * setLevel
	 * @param string $level
	 * @return this
	**/
	this.setLevel = function( level ) {
		this.level = level;
		return this;
	}
	
	/**
	 * ID functions
	**/
	this.setId = function( id ) {
		this.id = id;
	}
	this.getId = function() {
		return this.id;
	}
	
	/**
	 * _getHighlightColor
	 *
	 * @return string
	**/
	this._getHighlightColor = function() {
		switch( this.getLevel() ) {
			case 'success':		return '#3c763d';
			case 'info':		return '#31708f';
			case 'warning':		return '#8a6d3b';
			case 'danger':		return '#a94442';
		}
		return '#3c763d';
	}
	
	/**
	 * _getMessageContainer	
	 *
	 * return jQuery object of the message (container)
	**/
	this._getMessageContainer = function() {
		return $( this.messageContainerId );
	}

	/**
	 * _getContainer	
	 *
	 * return jQuery object of the message container
	**/
	this._getContainer = function() {
		return $( this.containerId );
	}
	
	this._registerListeners = function() {
		$(document).on('mMessage:' + this.getId() + ':hide', function( event, mMessageObject ){
			mMessageObject.hideNow();
		});
	}
	
	this.init( id );
}