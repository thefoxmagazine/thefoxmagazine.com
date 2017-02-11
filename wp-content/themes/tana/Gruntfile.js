module.exports = function(grunt){

	css_files = [
		'vendors/bootstrap/css/bootstrap.min.css',
		'vendors/bootstrap/css/bootstrap-theme.min.css',
		'vendors/font-awesome/css/font-awesome.min.css',
		'vendors/animate.css',
		'css/mep.css',
		'vendors/swiper/css/swiper.min.css',
		'vendors/magnific-popup/magnific-popup.css',
		'vendors/masterslider/style/masterslider.css',
		'vendors/masterslider/skins/default/ms-style.css',
	];

	js_files = [
		'vendors/bootstrap/js/bootstrap.min.js',
		'vendors/swiper/js/swiper.min.js',
		'vendors/magnific-popup/jquery.magnific-popup.min.js',
		'vendors/jquery.waypoints.min.js',
		'vendors/isotope.pkgd.min.js',
		'vendors/typed.min.js',
		'vendors/theia-sticky-sidebar.js',
		'vendors/circles.min.js',
		'vendors/jquery.stellar.min.js',
		'vendors/jquery.parallax.columns.js',
		'vendors/svg-morpheus.js',
		'vendors/jquery.hover3d.min.js',
		'vendors/masterslider/jquery.easing.min.js',
		'vendors/masterslider/masterslider.min.js'
	];

	scripts_file = [
		'js/scripts.js'
	];

	var watch_less_tasks = ['less:compile'];
	var csspath = grunt.option('csspath');
	var compiled_css = '';

	if( typeof(csspath) !== 'undefined' && csspath!='' ){
		watch_less_tasks = ['less:compile_replace', 'less:compile'];
		compiled_css = csspath + 'tana.css';
		console.log('Theme Compiled CSS: ', compiled_css);
	}

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Concating js files
		uglify: {
			pack_combine: {
				options: {
					beautify: true,
					mangle: false,
					compress: false,
					preserveComments: 'all'
				},
				src: js_files,
				dest: 'js/packages.js',
			},
			pack_compress: {
				src: js_files,
				dest: 'js/packages.min.js',
			},
			scripts_compress: {
				src: scripts_file,
				dest: 'js/scripts.min.js',
			}
		},

		cssmin: {
			options: {
				rebase: true
			},
			combine: {
				files: {
					'css/packages.min.css': css_files
				}
			}
		},

		// Less file compiler
		compiled_css_path: compiled_css,
		less: {
			compile: {
				options: {
					// sourceMap: true,
					// sourceMapFilename: 'css/default.css.map',
					// sourceMapURL: 'default.css.map',
					paths: ['css'],
					modifyVars: {
						"img-path": "'../'"
					}
				},
				files: {
					'css/default.css': 'less/style.less'
				}
			},
			compile_replace: {
				options: {
					paths: ['css'],
					modifyVars: {
						"img-path": "'/wp-content/themes/tana/'"
					}
				},
				files: {
					'<%= compiled_css_path %>': 'less/style.less'
				}
			}
		},

		// Build all files when Less file changes
		watch: {
			css: {
				files: css_files,
				tasks: ['cssmin']
			},
			less: {
				files: 'less/**/*.less',
				tasks: watch_less_tasks
			},
			js_packs: {
				files: js_files,
				tasks: ['uglify:pack_combine', 'uglify:pack_compress']
			},
			scripts: {
				files: scripts_file,
				tasks: ['uglify:scripts_compress']
			}
		}
	});


	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');

	defined_tasks = [
		'uglify:pack_combine',
		'uglify:pack_compress',
		'uglify:scripts_compress',
		'cssmin',
		'less:compile',
		'watch'
	];

	grunt.registerTask('default', defined_tasks);

}
