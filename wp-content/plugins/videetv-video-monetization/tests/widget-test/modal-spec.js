describe("Test settings modal", function () {
	var observable;
	var valueAccessor;
	var element;
	var settingModal;
	var widgetViewModel;
	var allBindings;
	var bindingContext;
	var template;
	beforeEach(function () {
		window.$ = jQuery;
		observable = ko.observable({
			id: 79674,
			length: "2:17",
			thumb: "http://video1source1.videe.tv/pcovers/158890706.jpg",
			title: "#77 7233 189 st, Surrey for Joe Pratap | Real Estate HD Video Tour",
			videoSrc: "http://video1source1.videe.tv/pvideo/53f95ee744ca561ec91d4681b2470f70.mp4",
			views: 0
		});
		valueAccessor = function () {
			return observable;
		};
		allBindings = {
			modalVisibility: false,
			isCopy: false,
			get: function (key) {
				return this[key]
			}
		};
		bindingContext = {
			$data: widgetViewModel,
			$parents: [],
			$rawData: widgetViewModel,
			$root: widgetViewModel,
			ko: ko
		};
		settingModal = ko.bindingHandlers.settingModal;

		element = $('<div class="modal-wrapper"></div>');
		template = $('<script type="text/html" id="videe-modal"></script>');
		element.append(template);
		console.log(element[0]);
		widgetViewModel = new WidgetViewModel(UserModel);
	});

	it("should create handler", function () {
		var fn = spyOn(ko.bindingHandlers.value, 'init');
		settingModal.init(element[0], valueAccessor, allBindings, null, bindingContext);

		expect(fn).toHaveBeenCalled();
	});

	/*it("should open modal", function () {
		settingModal.update(element[0], valueAccessor, allBindings, null, bindingConttext)
	});*/
});
