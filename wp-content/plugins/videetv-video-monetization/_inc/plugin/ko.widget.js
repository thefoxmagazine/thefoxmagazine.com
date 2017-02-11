var WidgetViewModel = function () {
	this.config = new Config();

	this.authorized = this.config.getToken();
	
	this.fetching = ko.observable(false);

	this.filters = new Filters();

	this.emptyMessage = ko.observable("");

	this.tabs = ['Videos', 'Playlists'];
	this.subTabs = [
		{
			name: 'Standard',
			domestic: 1,
			source: "general"
		},
		{
			name: 'Custom',
			domestic: 0,
			source: "private"
		},
		{
			name: 'Premium',
			domestic: 2,
			source: "premium"
		}
	];

	this.activeTab = ko.observable('Videos');
	this.activeSubTab = ko.observable('Standard');
	this.totalPages = ko.observable(0);
	this.currentPage = ko.observable(1);
	this.queryString = ko.observable("");

	this.modalModel = ko.observable();
	this.modalVisible = ko.observable(false);
	this.modalCopy = ko.observable(false);

	this.filtersVisible = ko.observable(false);

	this.Videos = VideosViewModel();
	this.Playlists = PlaylistsViewModel();

	// this.items = ko.observableArray();

	this.resetPage = function () {
		this.currentPage(1);
		this.filters.setFilter("start", this.currentPage());
		this.filters.setFilter("page", this.currentPage() - 1);
	}.bind(this);
	
	this.getThumbUrl = function(obj) {
		return getThumbUrl(obj);
	};
	
	this.getVideoUrl = function(obj) {
		return getVideoUrl(obj);
	};

	this.resetQuery = function () {
		this.queryString("");
		this.filters.setFilter("query", this.queryString());
	}.bind(this);

	this.changeTab = function (tab, event) {
		event && event.preventDefault();
		this.activeTab(tab);
		this.resetQuery();
		this.resetPage();
		this.filters.initialState();
		this.filtersVisible(false);
		this.receiveData();
	}.bind(this);

	this.changeSubTab = function (subTab, event) {
		event && event.preventDefault();
		this.activeSubTab(subTab.name);
		this.filters.setFilter("domestic", subTab.domestic);
		this.filters.setFilter("source", subTab.source);
		this.resetQuery();
		this.resetPage();
		this.filters.initialState();
		this.receiveData();
	}.bind(this);

	this.setPage = function () {
		var currentPage = this.currentPage();
		if (currentPage <= 0 || isNaN(currentPage) || currentPage === '') {
			this.currentPage(1);
		}
		if (currentPage > this.filters.SearchFilter.maxPages()) {
			this.currentPage(this.filters.SearchFilter.maxPages());
		}
		this.filters.setFilter("start", this.currentPage());
		this.filters.setFilter("page", this.currentPage() - 1);
		this.receiveData();
	}.bind(this);

	this.nextPage = function () {
		if (!this.currentPage() || this.currentPage() == this.filters.SearchFilter.maxPages()) {
			return false;
		}
		var page = parseInt(this.currentPage());
		if (page > this.filters.SearchFilter.maxPages()) {
			page = this.filters.SearchFilter.maxPages() - 1;
		}
		this.currentPage(page + 1);
		this.filters.setFilter("start", this.currentPage());
		this.filters.setFilter("page", this.currentPage() - 1);
		this.receiveData();
	}.bind(this);

	this.prevPage = function () {
		if (!this.currentPage() || this.currentPage() == 1) {
			return false;
		}
		var page = parseInt(this.currentPage());
		if (page <= 0) {
			page = 1;
			this.currentPage(page);
		} else {
			this.currentPage(page - 1);
		}
		this.filters.setFilter("start", this.currentPage());
		this.filters.setFilter("page", this.currentPage() - 1);
		this.receiveData();
	}.bind(this);

	this.lastPage = function () {
		this.currentPage(this.filters.SearchFilter.maxPages());
		this.filters.setFilter("start", this.currentPage());
		this.filters.setFilter("page", this.currentPage() - 1);
		this.receiveData();
	}.bind(this);

	this.firstPage = function () {
		this.currentPage(1);
		this.filters.setFilter("start", this.currentPage());
		this.filters.setFilter("page", this.currentPage() - 1);
		this.receiveData();
	};

	this.setQuery = function () {
		this.filters.setFilter("query", this.queryString());
		this.resetPage();
		this.receiveData();
	}.bind(this);

	this.openModal = function (isCopy, data) {
		
		this.modalModel(data);
		this.modalVisible(true);
		this.modalCopy(isCopy);
	}.bind(this);

	this.openFilters = function () {
		var visible = this.filtersVisible();
		this.filtersVisible(!visible);
	};

	//ToDo Cover tests
	this.uploadVideo = function (modal, event) {
		var uploadWindow = open(event.target.href);

		window.addEventListener('message', function (e) {
			switch (e.data) {
				case 'closeMe':
					uploadWindow.close();
					break;
				case 'uploaded':
					this.changeTab(this.tabs[0]);
					this.changeSubTab(this.subTabs[1]);
					break;
			}
		}.bind(this));
		window.addEventListener('beforeunload', function () {
			uploadWindow.close();
		});
	}.bind(this);

	this.receiveData = function () {
		if(this.authorized) {
			this.fetching(true);
			this[this.activeTab()].fetch(this.filters.getQuery(this.activeTab().toLowerCase())).then(function (response) {
				this.fetching(false);
				this.filters.setFilter("total", response.total);
				this.totalPages(this.filters.SearchFilter.maxPages());
			}.bind(this));			
		}

	};

	$.ajax({
		url: this.config.getApiUrl() + "videocategories?auth_token=" + this.config.getToken() + "&limit=100",
		dataType: 'json',
		success: function (data) {
			var results = data.items.map(function (item, index) {
				return {
					id: item.id,
					text: item.name
				}
			});
			this.filters.categoriesOptions(results);
		}.bind(this),
		error: function (error) {
			console.log(error);
		}
	});

	//ToDo Cover tests
	this.receiveData();


	/*== Filters ==*/

	this.submitFilters = function (viewModel, event) {
		this.setQuery();
		this.receiveData();
	};

	this.resetFilter = function (options, selected, viewModel, event) {
		if(options){
			selected(options()[0]);
		} else {
			selected('')
		}
	}
};

var Filters = function () {
	this.config = new Config();
	
	this.qualityOptions = ko.observableArray([
		{text: "360p", id: 360},
		{text: "480p", id: 480},
		{text: "720p", id: 720},
		{text: "1080p", id: 1080}
	]);
	this.durationOptions = ko.observableArray([
		{
			text: "Short ( < 4:00 )",
			id: '{"from": 0, "to": 240}'
		},
		{
			text: "Short ( 4 - 10:00 )",
			id: '{"from": 241, "to": 600}'
		},
		{
			text: "Short ( > 10:00 )",
			id: '{"from": 601}'
		}
	]);
	this.viewsOptions = ko.observableArray([
		{
			text: '<100',
			id: '{"from": 0, "to": 100}',
			disable: false
		},
		{
			text: '100 - 500',
			id: '{"from": 101, "to": 500}'
		},
		{
			text: '500 - 1000',
			id: '{"from": 501, "to": 1000}'
		},
		{
			text: '>1000',
			id: '{"from": 1001}'
		}
	]);
	this.licenseOptions = ko.observableArray([
		{text: 'CC BY', id: 'CC BY'},
		{text: 'CC BY-SA', id: 'CC BY-SA'},
		{text: 'CC BY-ND', id: 'CC BY-ND'},
		{text: 'CC BY-NC', id: 'CC BY-NC'},
		{text: 'CC BY-NC-ND', id: 'CC BY-NC-ND'},
		{text: 'CC BY-NC-SA', id: 'CC BY-NC-SA'},
		{text: 'CC0', id: 'CC0'}
	]);
	this.categoriesOptions = ko.observableArray([]);

	this.selectedLabels = ko.observableArray([]);
	this.selectedQuality = ko.observable();
	this.selectedDuration = ko.observable();
	this.selectedViews = ko.observable();
	this.selectedLicense = ko.observable();
	this.selectedDates = {
		from: ko.observable(),
		to: ko.observable()
	};

	this.initialState = function () {
		this.selectedLabels([]);
		this.selectedQuality('');
		this.selectedDuration('');
		this.selectedViews('');
		this.selectedLicense('');
		this.selectedDates.from('');
		this.selectedDates.to('');
	}.bind(this);

	this.getFilters = function () {
		return {
			"max_quality": this.selectedQuality(),
			"license": this.selectedLicense(),
			"length": this.selectedDuration() && JSON.parse(this.selectedDuration()),
			"views": this.selectedViews() && JSON.parse(this.selectedViews()),
			"categories.id": this.selectedLabels().length ? this.selectedLabels() : null,
			"date_created": ko.computed(function () {
				var dates = {};
				if (this.selectedDates.from()) {
					dates.from = this.selectedDates.from();
				}
				if (this.selectedDates.to()) {
					dates.to = this.selectedDates.to();
				}
				if (!Object.keys(dates).length) {
					return null
				}
				return dates
			}, this)
		}
	};

	this.getCleanFilters = function (filters) {
		var applyFilters = {};
		for (var key in filters) {
			var item;
			if (typeof filters[key] === "function") {
				item = filters[key]()
			} else {
				item = filters[key]
			}
			if (item) {
				applyFilters[key] = item;
			}
		}
		return applyFilters
	};

	this.SearchFilter = {
		limit: ko.observable(20),
		start: ko.observable(0),
		domestic: ko.observable(1),
		query: ko.observable(''),
		total: ko.observable(0),
		page: ko.observable(0),
		maxPages: ko.observable(0),
		property: ko.observable("date_created"),
		asc: ko.observable('DESC'),
		source: ko.observable()
	};

	//ToDo Cover tests
	this.getQuery = function (type) {
		var url = type + '?auth_token=' + this.config.getToken() +
			'&query=' + encodeURIComponent(this.SearchFilter.query().trim()) +
			'&applyFilter=' + JSON.stringify(this.getCleanFilters(this.getFilters())) +
			'&domestic=' + this.SearchFilter.domestic() +
			'&limit=' + this.SearchFilter.limit() +
			'&start=' + this.SearchFilter.start() +
			(this.SearchFilter.source() == 'premium' ? '&premium=true' : '') +
			'&sort=[{"property":"' + this.SearchFilter.property() + '","direction":"' + this.SearchFilter.asc() + '"}]';
		
			if (this.SearchFilter.query().trim().length > 0) {
				url += '&relevance=1';
			}
		return url;
	}.bind(this);
	//ToDo Cover tests
	this.setFilter = function (name, value) {
		switch (name) {
			case "start":
				this.SearchFilter[name]((value - 1) * this.SearchFilter.limit());
				break;
			case "total":
				this.SearchFilter[name](value);
				this.SearchFilter.maxPages(Math.ceil(this.SearchFilter.total() / this.SearchFilter.limit()));
				break;
			default:
				this.SearchFilter[name](value);
		}
	}.bind(this);
};
