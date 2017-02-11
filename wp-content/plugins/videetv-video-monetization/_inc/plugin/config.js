var Config = (function (defaultOptions) {
	
	var options = jQuery.extend({}, defaultOptions);
		
	var instance;

	
	return function ConstructSingletone() {
		
		this.getToken = function () {
			return options['videeToken'] ? options['videeToken']: null;
		};
		
		this.setToken = function(token) {
			options['videeToken'] = token;
		}
		
		this.getUserId = function () {
			return options['videeUserId'] ? options['videeUserId']: null;
		};
		
		this.setUserId = function(userId) {
			options['videeUserId'] = userId;
		}
		
		this.getPluginUrl = function () {
			return options['videePluginUrl'] ? options['videePluginUrl']: null;
		};
		
		this.setPluginUrl = function(pluginUrl) {
			options['videePluginUrl'] = pluginUrl;
		}
		
		this.getApiUrl = function() {
			return options['videeApiUrl'] ? options['videeApiUrl']: null;
		}
		
		this.setApiUrl = function(apiUrl) {
			options['videeApiUrl'] = apiUrl;
		}
		
		if (instance) {
			return instance;
		}
		
		if (this && this.constructor === ConstructSingletone) {
			instance = this;
		} else {
			return new ConstructSingletone();
		}
	
	}

})(defaultOptions);