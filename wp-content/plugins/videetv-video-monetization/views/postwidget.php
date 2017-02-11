<?php global $locator; if (!$locator) exit('Forbidden. hacking attempt'); ?>

<script type="text/javascript">
    window.$ = jQuery;
</script>

<div id="videe-widget" class="categorydiv">
    <!-- Unauthorize user-->
    <div class="unauthorized-error" data-bind="visible: !authorized">
        <h4>Want to monetize your website?</h4>
        <p>
            <a class="button button-orange" href=" <?php echo $locator->admin->getPageUrl('login'); ?> ">
                Connect Videe.TV account
            </a>
            <br>
            <a href="<?php echo $locator->admin->getPageUrl('help');?>" class="videe-learn-more">Learn more...</a>
        </p>
    </div>

    <!-- Main widget -->
    <div data-bind="visible: authorized">
        <a target="_blank"
           href="<?php echo $locator->admin->getPageUrl('upload');?>"
           data-bind="click: $root.uploadVideo"
           class="button button-primary videe-upload-btn">Upload</a>

        <ul id="videe-widget-tabs" class="category-tabs" data-bind="foreach: tabs">
            <li data-bind="css:{tabs: $root.activeTab() == $data}">
                <a href="#" data-bind="text: $data, click: $root.changeTab"></a>
            </li>
        </ul>

        <div id="videe-widget-videos" class="tabs-panel videe-widget"
             data-bind="visible: activeTab() == 'Videos'">
            <div id="videe-filter">
                <div class="button-group videe-filter-group"
                     data-bind="foreach: subTabs">
                    <button type="button" class="button videe-filter-button"
                            data-bind="text: $data.name, click: $root.changeSubTab, css:{active: $root.activeSubTab() == $data.name}"></button>
                </div>

                <div class="videe-filter-search">
                    <a href="#" data-bind="click: $root.openFilters"><<</a>
                    <input type="text" id="videe-filter-input" class="videe-filter-input" name="query_string" value=""
                           data-bind="textInput: queryString, valueUpdate: 'afterkeydown', queryset: setQuery"
                           placeholder="Search"/>
                    <!-- ko if: filtersVisible() -->
                    <div class="filters-wrapper">
                        <label>
                            <span class="label-text">Categories</span>
                            <select
                                name="categories"
                                id="categories-select"
                                data-tags="true"
                                data-allow-clear="true"
                                data-bind="optionsValue: 'id', optionsText: 'text',
                                selectedOptions: filters.selectedLabels,
                                options: filters.categoriesOptions,
                                select2: {multiple: true, width: '225px'}"></select>
                        </label>
                        <label>
                            <span class="label-text">Quality</span>
                            <select
                                name="quality"
                                id="quality-select"
                                data-allow-clear="true"
                                data-bind="select2: {width: '225px', options: filters.qualityOptions, minimumResultsForSearch: -1},
                                value: filters.selectedQuality">
                            </select>
                        </label>
                        <label>
                            <span class="label-text">Duration</span>
                            <select
                                name="duration"
                                id="duration-select"
                                data-allow-clear="true"
                                data-bind="select2: {width: '225px', options: filters.durationOptions, minimumResultsForSearch: -1},
                                optionsValue: 'id', optionsText: 'text',
                                value: filters.selectedDuration">
                            </select>
                        </label>
                        <!-- ko if: activeTab() == 'Videos' && activeSubTab() == 'Standard' -->
                        <label>
                            <span class="label-text">License</span>
                            <select
                                name="license"
                                id="license-select"
                                data-allow-clear="true"
                                data-bind="select2: {width: '225px', options: filters.licenseOptions, minimumResultsForSearch: -1},
                                value: filters.selectedLicense">
                            </select>
                        </label>
                        <!-- /ko -->
                        <label>
                            <span class="label-text">Views</span>
                            <select
                                name="views"
                                id="views-select"
                                data-allow-clear="true"
                                data-bind="select2: {width: '225px', options: filters.viewsOptions, minimumResultsForSearch: -1},
                                value: filters.selectedViews">
                            </select>
                        </label>
                        <label>
                            <span class="label-text">Upload date</span>
                            <span class="date-wrapper">
                                <span
                                    data-bind="text: filters.selectedDates.from, datepicker: filters.selectedDates.from"></span>
                                <!-- ko if: filters.selectedDates.from() -->
                                <span class="dashicons dashicons-dismiss" data-bind="click: resetFilter.bind($data, null, filters.selectedDates.from)"></span>
                                <!-- /ko -->
                            </span>
                            <span class="date-wrapper">
                                <span data-bind="text: filters.selectedDates.to, datepicker: filters.selectedDates.to"></span>
                                <!-- ko if: filters.selectedDates.to() -->
                                <span class="dashicons dashicons-dismiss" data-bind="click: resetFilter.bind($data, null, filters.selectedDates.to)"></span>
                                <!-- /ko -->
                            </span>
                        </label>
                        <div>
                            <button class="button button-primary"
                                    data-bind="click: submitFilters">Apply
                            </button>
                        </div>
                    </div>
                    <!-- /ko -->
                </div>
            </div>
            <div id="videe-videos-list" class="clearfix videe-list"
                 data-bind="css: {'videe-spinner videe-spinner-tiny': fetching}">
                <!-- ko if: fetching -->
                <div class="videe-spinner-rotator"></div>
                <!-- /ko -->

                <div data-bind="visible: (!fetching() && Videos.items().length > 0), foreach: Videos.items">
                    <div class="videe-item"
                         data-bind="component: { name: 'video-item', params: { params: $data, viewModel: $root, element: $element } }"></div>
                </div>

                <!-- ko if: !fetching() && Videos.items().length == 0 && activeTab() == 'Videos' && activeSubTab() == 'Standard' -->
                <h4 class="videe-no-videos">No matching results found.<br/>
                    Please modify your search criteria and try searching again.
                </h4>
                <!-- /ko -->
                <!-- ko if: !fetching() && activeTab() == 'Videos' && activeSubTab() == 'Premium' -->
                <h4 class="videe-no-videos">Coming soon!<br/>
                    Collection of high-quality videos in the most popular categories to impress your users.
                </h4>
                <!-- /ko -->
                <!-- ko if: !fetching() && Videos.items().length == 0 && activeTab() == 'Videos' && activeSubTab() == 'Custom' && filters.SearchFilter.query() !== ""-->
                <h4 class="videe-no-videos">No matching results found.<br/>
                    Please modify your search criteria and try searching again.
                </h4>
                <!-- /ko -->
                <!-- ko if: !fetching() && Videos.items().length == 0 && activeTab() == 'Videos' && activeSubTab() == 'Custom' && filters.SearchFilter.query() === ""-->
                <h4 class="videe-no-videos">No videos found.<br/>
                    <a target="_blank" href="<?php echo $locator->admin->getPageUrl('upload'); ?>"
                       data-bind="click: $root.uploadVideo">Upload</a> your first video.
                </h4>
                <!-- /ko -->
            </div>
        </div>

        <div id="videe-widget-playlists" class="tabs-panel videe-widget"
             data-bind="visible: activeTab() == 'Playlists'">
            <div id="videe-filter">
                <div class="videe-filter-search">
                    <input type="text" id="videe-filter-input" class="videe-filter-input" name="query_string" value=""
                           data-bind="textInput: queryString, valueUpdate: 'afterkeydown', queryset: setQuery"
                           placeholder="Search"/>
                </div>
            </div>
            <div id="videe-playlists-list" class="videe-list clearfix"
                 data-bind="css: {'videe-spinner videe-spinner-tiny': fetching}">
                <!-- ko if: fetching -->
                <div class="videe-spinner-rotator"></div>
                <!-- /ko -->

                <div data-bind="visible: (!fetching() && Playlists.items().length > 0), foreach: Playlists.items">
                    <div class="videe-item"
                         data-bind="component: { name: 'playlist-item', params: { params: $data, viewModel: $root, element: $element } }"></div>
                </div>

                <!-- ko if: !fetching() && Playlists.items().length == 0 && activeTab() == 'Playlists' && filters.SearchFilter.query() === "" -->
                <h4 class="videe-no-videos">No playlists found.<br/>
                    <a target="_blank" href="<?php echo $locator->admin->getPageUrl('playlist');?>">Create</a> your
                    first playlist.
                </h4>
                <!-- /ko -->
                <!-- ko if: !fetching() && Playlists.items().length == 0 &&  activeTab() == 'Playlists' && filters.SearchFilter.query() !== "" -->
                <h4 class="videe-no-videos">No matching results found.<br/>
                    Please modify your search criteria and try searching again.
                </h4>
                <!-- /ko -->
            </div>
        </div>

        <div id="videe-pager">
            <div class="tablenav">
                <div class="tablenav-pages">
                    <div class="pagination-links">
                        <a data-page="0" id="videe-pager-first-page" class="first-page" title="Go to the first page"
                           data-bind="click: firstPage"
                           href="#">«</a>
                        <a data-page="0" id="videe-pager-prev-page" class="prev-page" title="Go to the previous page"
                           data-bind="click: prevPage"
                           href="#">‹</a>
                        <div class="paging-input">
                            <label for="videe-pager-current" class="screen-reader-text">Select Page</label>
                            <input class="current-page" id="videe-pager-current" title="Current page" type="text"
                                   data-bind="textInput: currentPage, enterkey: setPage"
                                   name="paged" value="1" size="3"/> of
                            <span id="videe-pager-total" data-bind="text: totalPages" class="total-pages">0</span>
                        </div>
                        <a data-page="0" id="videe-pager-next-page" class="next-page" title="Go to the next page"
                           data-bind="click: nextPage"
                           href="#">›</a>
                        <a data-page="0" id="videe-pager-last-page" class="last-page" title="Go to the last page"
                           data-bind="click: lastPage"
                           href="#">»</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ko if: modalVisible -->
    <div data-bind="settingModal: modalModel, modalVisibility: modalVisible, type: activeTab, isCopy: modalCopy"></div>
    <!-- /ko -->
</div>

<script type="text/html" id="videe-modal">

    <div class="modal-wrapper">
        <div id="modal-settings" class="mce-container modal mce-panel mce-floatpanel mce-window mce-in" hidefocus="1"
             role="dialog"
             aria-label="Edit Video"
             data-bind="visible: isShowing, style: { width: '780px', height: (isPlaylist ? '458px' : '398px') }"
             style="border-width: 1px; z-index: 100101; display: none">
            <div class="mce-reset" role="application">
                <div class="mce-window-head">
                    <div class="mce-title">Edit Video 
						<span class="videe-settings-notice" >Please note! The advertisements may not be shown if the player size is less than 300x250px</span>
					</div>
                    <div class="mce-dragh"></div>
                    <button type="button" class="mce-close" aria-hidden="true"
                            data-bind="click: modalDismiss">
                        <i class="mce-ico mce-i-remove"></i>
                    </button>
                </div>
                <div class="mce-container-body mce-window-body">
                    <div class="mce-container">
                        <div class="modal-video-setting clearfix" id="modal-settings">
                            <div class="videe-video-wrap">

                                <!-- ko if: isPlaylist -->
                                <video class="videe-video" controls
                                       data-bind="attr: { src: mediaObj.getCurrentItemVideoUrls()[activeVideo()]  }">
                                </video>								
                                <div class="playlist-ui" data-bind="foreach: mediaObj.getCurrentItemThumbUrls()">
                                    <img class="videe-item-image playlist" height="24" width="24" src=""
                                         data-bind="click: setVideo.bind($data, $index), attr: { src: $data }"/>
                                </div>
                                <!-- /ko -->
								<!-- ko ifnot: isPlaylist -->
                                <video class="videe-video" controls
                                       data-bind="attr: { src: mediaObj.getCurrentItemVideoUrl() }">
                                </video>								
								<!-- /ko -->
		
                            </div>
                            <div>
                                <form class="videe-settings">
                                    <ul class="videe-settings-list">
                                        <li class="videe-settings-item">
                                            <div class="videe-setting-name"
                                                 data-bind="click: accordion.bind($data, 'visual')">
                                                Visual settings
                                            </div>
                                            <div class="collapsed"
                                                 data-bind="css:{ 'expanded': accordionActive() == 'visual'}">
                                                <label><span class="setting-label">Size:</span>
                                                    <input type="text" name="width" required
                                                           data-bind="textInput: settings.width, preventchars: settings.width, disable: settings.autosize"
                                                           class="width size-setting-field" value="">
                                                    <span class="size-setting-cross">x</span>
                                                    <input type="text" name="height" required
                                                           data-bind="textInput: settings.height, preventchars: settings.height, disable: settings.autosize"
                                                           class="height size-setting-field" value="">
                                                </label>
												<label><input type="checkbox" name="autosize"
                                                              data-bind="checked: settings.autosize">
                                                    Autosize
                                                </label>
                                                <!--<label><span class="setting-label">SM:</span>
                                                    <input type="text" name="smwidth"
                                                           data-bind="textInput: settings.smWidth, preventchars: settings.smWidth"
                                                           class="width size-setting-field" value="">
                                                    <span class="size-setting-cross">x</span>
                                                    <input type="text" name="smheight"
                                                           data-bind="textInput: settings.smHeight, preventchars: settings.smHeight"
                                                           class="height size-setting-field" value="">
                                                </label>
                                                <label><span class="setting-label">MD:</span>
                                                    <input type="text" name="mdwidth"
                                                           data-bind="textInput: settings.mdWidth, preventchars: settings.mdWidth"
                                                           class="width size-setting-field" value="">
                                                    <span class="size-setting-cross">x</span>
                                                    <input type="text" name="mdheight"
                                                           data-bind="textInput: settings.mdHeight, preventchars: settings.mdHeight"
                                                           class="height size-setting-field" value="">
                                                </label>
                                                <label><span class="setting-label">LG:</span>
                                                    <input type="text" name="lgwidth"
                                                           data-bind="textInput: settings.lgWidth, preventchars: settings.lgWidth"
                                                           class="width size-setting-field" value="">
                                                    <span class="size-setting-cross">x</span>
                                                    <input type="text" name="lgheight"
                                                           data-bind="textInput: settings.lgHeight, preventchars: settings.lgHeight"
                                                           class="height size-setting-field" value="">
                                                </label>-->
                                                <!--<label for="playlist-type"><span
                                                        class="setting-label">Playlist:</span>
                                                    <select name="playlist" id="playlist-type"
                                                            data-bind="value: settings.playlistMode">
                                                        <option value=""></option>
                                                        <option value="horizontal">Horizontal</option>
                                                        <option value="vertical">Vertical</option>
                                                    </select>
                                                </label>-->
                                            </div>
                                        </li>
                                        <li class="videe-settings-item">
                                            <div class="videe-setting-name"
                                                 data-bind="click: accordion.bind($data, 'playback')">
                                                Playback settings
                                            </div>
                                            <div class="collapsed"
                                                 data-bind="css:{ 'expanded': accordionActive() == 'playback'}">
                                                <label>Volume:<br/>
                                                    <i class="settings-icon dashicons"
                                                       data-bind="css: { 'dashicons-controls-volumeon': !settings.mute(), 'dashicons-controls-volumeoff': settings.mute() }"></i>
                                                    <input type="range"
                                                           data-bind="textInput: settings.volume, event: {input: changeVolume, change: changeVolume}"
                                                           name="volume" class="volume"
                                                           min="0" max="100" step="5" value="">
                                                    <span id="volume-value" data-bind="text: settings.volume"></span>%
                                                </label>
                                                <label><input type="checkbox" name="mute" class="mute"
                                                              data-bind="checked: settings.mute, click: setMute">
                                                    Mute
                                                </label>
                                                <label><input type="checkbox" name="autoplay" class="autoplay"
                                                              data-bind="checked: settings.autoplay">
                                                    Autoplay
                                                </label>
                                                <label><input type="checkbox" name="loop" class="loop"
                                                              data-bind="checked: settings.loop">
                                                    Loop
                                                </label>
                                            </div>
                                        </li>
                                        <li class="videe-settings-item">
                                            <div class="videe-setting-name"
                                                 data-bind="click: accordion.bind($data, 'monetization')">
                                                Monetization settings
                                            </div>
                                            <div class="collapsed"
                                                 data-bind="css:{ 'expanded': accordionActive() == 'monetization'}">
                                                <label><input type="checkbox" name="background"
                                                              data-bind="checked: settings.background"
                                                              class="background">
                                                    VPAID Background
                                                    <i class="settings-icon dashicons dashicons-editor-help"></i>
                                                    <span class="tooltip">Recommended for usage together with async attribute. In case the user clicks play button before the ad has loaded, it will keep loading in the background mode. The video will be paused only when the ad starts</span>
                                                </label>
                                                <label><input type="checkbox" name="async" class="async"
                                                              data-bind="checked: settings.async">
                                                    VPAID Async
                                                    <i class="settings-icon dashicons dashicons-editor-help"></i>
                                                    <span class="tooltip">Recommended when autoplay is disabled. The attribute allows to load ads independently of the video loading, that dicreases the site’s page load time.</span>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mce-container mce-panel mce-foot" hidefocus="1" tabindex="-1" role="group">
                    <div class="mce-container-body" style="height: 50px; padding: 10px 20px">
                        <button type="button" tabindex="-1"
                                class="button videe-button"
                                data-bind="click: modalDismiss">Cancel
                        </button>
                        <button type="button" tabindex="-1" style="margin-right: 10px"
                                class="button videe-button button-primary"
                                data-bind="click: modalSubmit">Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop"></div>
    </div>

</script>

<script>
    var widgetViewModel = new WidgetViewModel();
    ko.applyBindings(widgetViewModel);
</script>
