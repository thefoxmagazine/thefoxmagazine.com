var UserModel = function () {
	var videeToken = 'a44e063f0c25b4abfdfa8bc5410c118f';
	var videeUserId = 13859;
	var videePluginUrl = 'http://korenuk.com/wp-content/plugins/videe/';
	window.videePluginUrl = 'http://korenuk.com/wp-content/plugins/videe/';

	this.getToken = function () {
		return videeToken;
	};
	this.getUserId = function () {
		return videeUserId;
	};
	this.getPluginURL = function () {
		return videePluginUrl;
	};
};

describe("Test Filters", function () {

	var widgetViewModel;
	var event;
	var user = new UserModel();
	beforeEach(function () {
		window.$ = jQuery;
		filters = new Filters(user);
		event = {
			preventDefault: function () {}
		};
	});

	it("should get query string for Videos", function () {
		var queryString = 'videos?auth_token=a44e063f0c25b4abfdfa8bc5410c118f' +
			'&query=&domestic=1&limit=20&start=0&sort=[{"property":"date_created","direction":"DESC"}]';
		expect(filters.getQuery('videos')).toEqual(queryString);
	});

	it("should get query string for Playlists", function () {
		var queryString = 'playlists?auth_token=a44e063f0c25b4abfdfa8bc5410c118f' +
			'&query=&domestic=1&limit=20&start=0&sort=[{"property":"date_created","direction":"DESC"}]';
		expect(filters.getQuery('playlists')).toEqual(queryString);
	});

	it("should set filter limit", function () {
		filters.setFilter('limit', 30);
		expect(filters.SearchFilter.limit()).toEqual(30);
	});

	it("should set filter start", function () {
		var limit = 20;
		var start = 10;
		filters.setFilter('limit', limit);
		filters.setFilter('start', start);

		expect(filters.SearchFilter.start()).toEqual(180);
	});

	it("should set filter domestic", function () {
		filters.setFilter('domestic', 2);
		expect(filters.SearchFilter.domestic()).toEqual(2);
	});

	it("should set filter query", function () {
		filters.setFilter('query', "abc");
		expect(filters.SearchFilter.query()).toBe('abc');
	});

	it("should set filter total", function () {
		filters.setFilter('limit', 10);
		filters.setFilter('total', 43);

		expect(filters.SearchFilter.total()).toEqual(43);
		expect(filters.SearchFilter.maxPages()).toEqual(5);
	});

	it("should set filter page", function () {
		filters.setFilter('page', 5);
		expect(filters.SearchFilter.page()).toEqual(5);
	});

	it("should set filter maxPages", function () {
		filters.setFilter('maxPages', 10);
		expect(filters.SearchFilter.maxPages()).toEqual(10);
	});

	it("should set filter property", function () {
		filters.setFilter('property', "date_updated");
		expect(filters.SearchFilter.property()).toBe("date_updated");
	});

	it("should set filter asc", function () {
		filters.setFilter('asc', "ASC");
		expect(filters.SearchFilter.asc()).toBe("ASC");
	});

	it("should set filter source (general)", function () {
		var queryString = 'videos?auth_token=a44e063f0c25b4abfdfa8bc5410c118f' +
			'&query=&domestic=1&limit=20&start=0&sort=[{"property":"date_created","direction":"DESC"}]';
		filters.setFilter('source', "general");
		expect(filters.SearchFilter.source()).toBe("general");
		expect(filters.getQuery('videos')).toBe(queryString);
	});

	it("should set filter source (premium)", function () {
		var queryString = 'videos?auth_token=a44e063f0c25b4abfdfa8bc5410c118f' +
			'&query=&domestic=1&limit=20&start=0&premium=true&sort=[{"property":"date_created","direction":"DESC"}]';
		filters.setFilter('source', "premium");
		expect(filters.SearchFilter.source()).toBe("premium");
		expect(filters.getQuery('videos')).toBe(queryString);
	});

});
