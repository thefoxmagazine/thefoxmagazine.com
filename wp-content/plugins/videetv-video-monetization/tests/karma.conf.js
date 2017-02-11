module.exports = function (config) {
    'use strict';
    config.set({
        singleRun: true,
        browsers: ['PhantomJS'],
        // browsers: ['Chrome'],
        frameworks: ['jasmine'],
        browserNoActivityTimeout: 10000,
        reporters: [
            // 'progress',
            'dots',
            'html',
            'coverage'
        ],

        preprocessors: {
            '../_inc/plugin/*.js': [
                'coverage'
            ]
        },

        coverageReporter: {
            reporters: [
                {
                    type: 'text-summary'
                },
                {
                    type: 'html',
                    dir: 'coverage/'
                }
            ]
        },

        plugins: [
            'karma-jasmine',
            'karma-phantomjs-launcher',
            'karma-chrome-launcher',
            'karma-coverage',
            'karma-html-reporter',
            'karma-coverage'
        ],

        phantomjsLauncher: {
            flags: ['--web-security=no']
        },

        files: [
            './../_inc/libs/knockout-3.4.0.js',
            './libs/jquery.js',
            './../_inc/plugin/utils.js',
            './../_inc/plugin/ko.modal.widget.js',
            './../_inc/plugin/videosViewModel.js',
            './../_inc/plugin/playlistsViewModel.js',
            './../_inc/plugin/ko.widget.js',
            './widget-test/filters-spec.js',
            './widget-test/widget-spec.js',
            './widget-test/modal-spec.js'
        ]
    });
};
