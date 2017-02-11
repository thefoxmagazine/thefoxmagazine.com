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

var NullUserModel = function () {
	var videeToken = '';
	var videeUserId = null;
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

describe("Test videe", function () {
	it("Knockout should be exists", function () {
		expect(window.ko).toBeDefined();
	});

	describe("Widget", function () {

		it("should be loaded", function () {
			expect(WidgetViewModel).toBeDefined();
		});

		describe("functionality", function () {

			var widgetViewModel;
			var event;
			beforeEach(function () {
				window.$ = jQuery;
				widgetViewModel = new WidgetViewModel(UserModel);
				event = {
					preventDefault: function () {}
				};
			});

			it("view-model instance created", function () {
				expect(widgetViewModel).not.toBeUndefined();
			});

			it("User model instance created", function () {
				expect(widgetViewModel.user).not.toBeUndefined();
				expect(widgetViewModel.user).not.toBeNull();
				expect(widgetViewModel.authorized).not.toBeNull();
			});

			it("User model without session created", function () {
				widgetViewModel = new WidgetViewModel(NullUserModel);
				expect(widgetViewModel.user).not.toBeUndefined();
				expect(widgetViewModel.user).not.toBeNull();
				expect(widgetViewModel.authorized).toBeNull();
			});

			it("Filters instance created", function () {
				expect(widgetViewModel.filters).not.toBeUndefined();
			});

			it("Video view-model instance created", function () {
				expect(widgetViewModel.Videos).not.toBeUndefined();
			});

			it("Playlists view-model instance created", function () {
				expect(widgetViewModel.Playlists).not.toBeUndefined();
			});

			it("resetPage method should reset page search parameters", function () {
				widgetViewModel.resetPage();

				expect(widgetViewModel.currentPage()).toEqual(1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(0);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(0);
			});

			it("resetPage method should reset query search parameters", function () {
				widgetViewModel.resetQuery();

				expect(widgetViewModel.queryString()).toEqual("");
				expect(widgetViewModel.filters.SearchFilter.query()).toEqual("");
			});

			it("tab should be changed to Videos", function () {
				var prevent = spyOn(event, 'preventDefault');
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var resetPage = spyOn(widgetViewModel, 'resetPage');
				var resetQuery = spyOn(widgetViewModel, 'resetQuery');

				widgetViewModel.changeTab(widgetViewModel.tabs[0], event);
				expect(widgetViewModel.activeTab()).toEqual('Videos');

				expect(prevent).toHaveBeenCalled();
				expect(resetPage).toHaveBeenCalled();
				expect(resetQuery).toHaveBeenCalled();

				expect(widgetViewModel.queryString()).toEqual("");
				expect(widgetViewModel.filters.SearchFilter.query()).toEqual("");

				expect(widgetViewModel.currentPage()).toEqual(1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(0);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(0);

				expect(fetch).toHaveBeenCalled();
			});

			it("tab should be changed to Playlists", function () {
				var prevent = spyOn(event, 'preventDefault');
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var resetPage = spyOn(widgetViewModel, 'resetPage');
				var resetQuery = spyOn(widgetViewModel, 'resetQuery');

				widgetViewModel.changeTab(widgetViewModel.tabs[1], event);
				expect(widgetViewModel.activeTab()).toEqual('Playlists');

				expect(prevent).toHaveBeenCalled();
				expect(resetPage).toHaveBeenCalled();
				expect(resetQuery).toHaveBeenCalled();

				expect(widgetViewModel.queryString()).toEqual("");
				expect(widgetViewModel.filters.SearchFilter.query()).toEqual("");

				expect(widgetViewModel.currentPage()).toEqual(1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(0);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(0);

				expect(fetch).toHaveBeenCalled();
			});

			it("sub tab should be changed to Standard", function () {
				var prevent = spyOn(event, 'preventDefault');
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var resetPage = spyOn(widgetViewModel, 'resetPage');
				var resetQuery = spyOn(widgetViewModel, 'resetQuery');

				var domestic = widgetViewModel.filters.SearchFilter.domestic;
				var source = widgetViewModel.filters.SearchFilter.source;

				widgetViewModel.changeSubTab(widgetViewModel.subTabs[0], event);
				expect(widgetViewModel.activeSubTab()).toEqual('Standard');
				expect(domestic()).toEqual(1);
				expect(source()).toEqual('general');

				expect(prevent).toHaveBeenCalled();
				expect(resetPage).toHaveBeenCalled();
				expect(resetQuery).toHaveBeenCalled();

				expect(widgetViewModel.queryString()).toEqual("");
				expect(widgetViewModel.filters.SearchFilter.query()).toEqual("");

				expect(widgetViewModel.currentPage()).toEqual(1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(0);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(0);

				expect(fetch).toHaveBeenCalled();
			});

			it("sub tab should be changed to Custom", function () {
				var prevent = spyOn(event, 'preventDefault');
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var resetPage = spyOn(widgetViewModel, 'resetPage');
				var resetQuery = spyOn(widgetViewModel, 'resetQuery');

				var domestic = widgetViewModel.filters.SearchFilter.domestic;
				var source = widgetViewModel.filters.SearchFilter.source;

				widgetViewModel.changeSubTab(widgetViewModel.subTabs[1], event);
				expect(widgetViewModel.activeSubTab()).toEqual('Custom');
				expect(domestic()).toEqual(0);
				expect(source()).toEqual('private');

				expect(prevent).toHaveBeenCalled();
				expect(resetPage).toHaveBeenCalled();
				expect(resetQuery).toHaveBeenCalled();

				expect(widgetViewModel.queryString()).toEqual("");
				expect(widgetViewModel.filters.SearchFilter.query()).toEqual("");

				expect(widgetViewModel.currentPage()).toEqual(1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(0);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(0);

				expect(fetch).toHaveBeenCalled();
			});

			it("sub tab should be changed to Premium", function () {
				var prevent = spyOn(event, 'preventDefault');
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var resetPage = spyOn(widgetViewModel, 'resetPage');
				var resetQuery = spyOn(widgetViewModel, 'resetQuery');

				var domestic = widgetViewModel.filters.SearchFilter.domestic;
				var source = widgetViewModel.filters.SearchFilter.source;

				widgetViewModel.changeSubTab(widgetViewModel.subTabs[2], event);

				expect(widgetViewModel.activeSubTab()).toEqual('Premium');
				expect(domestic()).toEqual(2);
				expect(source()).toEqual('premium');

				expect(prevent).toHaveBeenCalled();
				expect(resetPage).toHaveBeenCalled();
				expect(resetQuery).toHaveBeenCalled();

				expect(widgetViewModel.queryString()).toEqual("");
				expect(widgetViewModel.filters.SearchFilter.query()).toEqual("");

				expect(widgetViewModel.currentPage()).toEqual(1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(0);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(0);

				expect(fetch).toHaveBeenCalled();
			});

			it("should set correct page", function () {
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var start = (2 - 1) * widgetViewModel.filters.SearchFilter.limit();

				widgetViewModel.currentPage(2);
				widgetViewModel.filters.SearchFilter.maxPages(5);
				widgetViewModel.setPage();

				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(start);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(1);
				expect(fetch).toHaveBeenCalled();
			});

			it("set page < 0, page should be 1", function () {
				widgetViewModel.currentPage(-1);
				widgetViewModel.filters.SearchFilter.maxPages(5);
				widgetViewModel.setPage();

				expect(widgetViewModel.currentPage()).toEqual(1);
			});

			it("set page 0, page should be 1", function () {
				widgetViewModel.currentPage(0);
				widgetViewModel.filters.SearchFilter.maxPages(5);
				widgetViewModel.setPage();

				expect(widgetViewModel.currentPage()).toEqual(1);
			});

			it("set empty page, page should be 1", function () {
				widgetViewModel.currentPage("");
				widgetViewModel.filters.SearchFilter.maxPages(5);
				widgetViewModel.setPage();

				expect(widgetViewModel.currentPage()).toEqual(1);
			});

			it("set NaN page, page should be 1", function () {
				widgetViewModel.currentPage("abc");
				widgetViewModel.filters.SearchFilter.maxPages(5);
				widgetViewModel.setPage();

				expect(widgetViewModel.currentPage()).toEqual(1);
			});

			it("set page > max pages, page should be max pages", function () {
				widgetViewModel.currentPage(6);
				widgetViewModel.filters.SearchFilter.maxPages(5);
				widgetViewModel.setPage();

				expect(widgetViewModel.currentPage()).toEqual(5);
			});

			it("should set next page (page < max pages), page should be next", function () {
				var page = 1;
				var maxPages = 5;
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var start = ((page + 1) - 1) * widgetViewModel.filters.SearchFilter.limit();

				widgetViewModel.currentPage(page);
				widgetViewModel.filters.SearchFilter.maxPages(maxPages);

				widgetViewModel.nextPage();

				expect(widgetViewModel.currentPage()).toEqual(page + 1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(start);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual((page + 1) - 1);
				expect(fetch).toHaveBeenCalled();
			});

			it("should set next page (page == max pages), page should not be changed", function () {
				var page = 5;
				var maxPages = 5;
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var start = (page - 1) * widgetViewModel.filters.SearchFilter.limit();

				widgetViewModel.currentPage(page);
				widgetViewModel.filters.SearchFilter.maxPages(maxPages);

				widgetViewModel.nextPage();

				expect(widgetViewModel.currentPage()).toEqual(page);
				expect(fetch).not.toHaveBeenCalled();
			});

			it("should set next page (page > max pages), page should be max", function () {
				var page = 55;
				var maxPages = 5;
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var start = (maxPages - 1) * widgetViewModel.filters.SearchFilter.limit();

				widgetViewModel.currentPage(page);
				widgetViewModel.filters.SearchFilter.maxPages(maxPages);

				widgetViewModel.nextPage();

				expect(widgetViewModel.currentPage()).toEqual(maxPages);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(start);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(maxPages - 1);
				expect(fetch).toHaveBeenCalled();
			});

			it("should set prev page (page < max pages), page should be previous", function () {
				var page = 3;
				var maxPages = 5;
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var start = ((page - 1) - 1) * widgetViewModel.filters.SearchFilter.limit();

				widgetViewModel.currentPage(page);
				widgetViewModel.filters.SearchFilter.maxPages(maxPages);

				widgetViewModel.prevPage();

				expect(widgetViewModel.currentPage()).toEqual(page - 1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(start);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual((page - 1) - 1);
				expect(fetch).toHaveBeenCalled();
			});

			it("should set prev page (page == 1), page should not be changed", function () {
				var page = 1;
				var maxPages = 5;
				var fetch = spyOn(widgetViewModel, 'receiveData');

				widgetViewModel.currentPage(page);
				widgetViewModel.filters.SearchFilter.maxPages(maxPages);

				widgetViewModel.prevPage();

				expect(widgetViewModel.currentPage()).toEqual(page);
				expect(fetch).not.toHaveBeenCalled();
			});

			it("should set prev page (page < 0), page should be 1", function () {
				var page = -1;
				var maxPages = 5;
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var start = (1 - 1) * widgetViewModel.filters.SearchFilter.limit();

				widgetViewModel.currentPage(page);
				widgetViewModel.filters.SearchFilter.maxPages(maxPages);

				widgetViewModel.prevPage();

				expect(widgetViewModel.currentPage()).toEqual(1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(start);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(1 - 1);
				expect(fetch).toHaveBeenCalled();
			});

			it("should set prev page (page == 0), page should be 1", function () {
				var page = "0";
				var maxPages = 5;
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var start = (1 - 1) * widgetViewModel.filters.SearchFilter.limit();

				widgetViewModel.currentPage(page);
				widgetViewModel.filters.SearchFilter.maxPages(maxPages);

				widgetViewModel.prevPage();

				expect(widgetViewModel.currentPage()).toEqual(1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(start);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(1 - 1);
				expect(fetch).toHaveBeenCalled();
			});

			it("should set last page", function () {
				var maxPages = 10;
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var start = (maxPages - 1) * widgetViewModel.filters.SearchFilter.limit();

				widgetViewModel.currentPage(5);
				widgetViewModel.filters.SearchFilter.maxPages(maxPages);

				widgetViewModel.lastPage();

				expect(widgetViewModel.currentPage()).toEqual(maxPages);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(start);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(maxPages - 1);
				expect(fetch).toHaveBeenCalled();
			});

			it("should set first page", function () {
				var maxPages = 10;
				var fetch = spyOn(widgetViewModel, 'receiveData');
				var start = (1 - 1) * widgetViewModel.filters.SearchFilter.limit();

				widgetViewModel.currentPage(5);
				widgetViewModel.filters.SearchFilter.maxPages(maxPages);

				widgetViewModel.firstPage();

				expect(widgetViewModel.currentPage()).toEqual(1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(start);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(1 - 1);
				expect(fetch).toHaveBeenCalled();
			});

			it("should set query string", function () {
				var query = "abc";
				var resetPage = spyOn(widgetViewModel, 'resetPage');
				var fetch = spyOn(widgetViewModel, 'receiveData');

				widgetViewModel.queryString(query);
				widgetViewModel.setQuery();

				expect(widgetViewModel.queryString()).toEqual(query);
				expect(widgetViewModel.filters.SearchFilter.query()).toEqual(query);

				expect(resetPage).toHaveBeenCalled();

				expect(widgetViewModel.currentPage()).toEqual(1);
				expect(widgetViewModel.filters.SearchFilter.start()).toEqual(0);
				expect(widgetViewModel.filters.SearchFilter.page()).toEqual(0);

				expect(fetch).toHaveBeenCalled();
			});

			it("settings modal should be opened (insert video)", function () {
				var data = {
					id: 78015,
					length: "1:25",
					thumb: "http://video1source1.videe.tv/pcovers/155341158.jpg",
					title: "Cem Bayoglu Backstage w/Ferman Akgul - Istemem Soz Sevmeni Music Video",
					videoSrc: "http://video1source1.videe.tv/pvideo/8f07374b2ee510c9bed0e844ec91de4e.mp4",
					views: 0
				};
				var isCopy = false;

				widgetViewModel.openModal(isCopy, data);

				expect(widgetViewModel.modalModel()).toEqual(data);
				expect(widgetViewModel.modalModel()).toBeTruthy();
				expect(widgetViewModel.modalCopy()).toEqual(isCopy);
			});

			it("settings modal should be opened (copy video)", function () {
				var data = {
					id: 78015,
					length: "1:25",
					thumb: "http://video1source1.videe.tv/pcovers/155341158.jpg",
					title: "Cem Bayoglu Backstage w/Ferman Akgul - Istemem Soz Sevmeni Music Video",
					videoSrc: "http://video1source1.videe.tv/pvideo/8f07374b2ee510c9bed0e844ec91de4e.mp4",
					views: 0
				};
				var isCopy = true;

				widgetViewModel.openModal(isCopy, data);

				expect(widgetViewModel.modalModel()).toEqual(data);
				expect(widgetViewModel.modalModel()).toBeTruthy();
				expect(widgetViewModel.modalCopy()).toEqual(isCopy);
			});
			
			it("should fetch data from API (Videos)", function (done) {

				var URL = 'https://vertamedia.com/api/';
				var video = "Videos";
				var queryString = 'videos?auth_token=' + widgetViewModel.user.getToken() +
					'&query=&domestic=1&limit=20&start=0&sort=[{"property":"date_created","direction":"DESC"}]';

				widgetViewModel.activeTab(video);
				widgetViewModel.receiveData();

				expect(widgetViewModel.fetching()).toBeTruthy();
				expect(widgetViewModel.authorized).not.toBeNull();
				$.ajax({
					url: URL + queryString,
					method: 'get',
					success: function (response) {
						widgetViewModel.fetching(false);
						widgetViewModel.filters.setFilter("total", response.total);
						widgetViewModel.totalPages(widgetViewModel.filters.SearchFilter.maxPages());

						var fetch = spyOn(widgetViewModel[video], 'fetch').and.returnValue($.when(response));
						widgetViewModel[video].fetch(widgetViewModel.filters.getQuery(widgetViewModel.activeTab().toLowerCase()));
						expect(fetch).toHaveBeenCalledWith(queryString);

						expect(response).toBeDefined();
						expect(widgetViewModel.fetching()).toBeFalsy();
						expect(widgetViewModel.filters.SearchFilter.total()).toEqual(response.total);
						expect(widgetViewModel.totalPages()).toEqual(widgetViewModel.filters.SearchFilter.maxPages());
						done();
					}
				});
			});

		});

	});
});
