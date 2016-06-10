/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	/**
	 * FIles added to WordPress SVN, don't inlucde 'assets/**' here.
	 * @type {Array}
	 */
	var svn_files_list = [
		'css/**',
		'inc/**',
		'js/**',
		'readme.txt',
		'<%= pkg.main_file %>'
	];

	grunt.initConfig({

		pkg: grunt.file.readJSON( 'package.json' ),

		// Setting folder templates.
		dirs: {
			js: 'js',
			css: 'css',
			images: 'images'
		},

		// Other options.
		options: {
			text_domain: 'really-simple-image-widget'
		},

		clean: {
			post_build: [
				'build'
			],
			remove_trunk:[
				'build/<%= pkg.name %>/trunk/'
			]
		},
		copy: {
			svn_trunk: {
				options: {
					mode: true
				},
				expand: true,
				src: svn_files_list,
				dest: 'build/<%= pkg.name %>/trunk/'
			},
			svn_tag: {
				options: {
					mode: true
				},
				expand: true,
				src: svn_files_list,
				dest: 'build/<%= pkg.name %>/tags/<%= pkg.version %>/'
			}
		},
		gittag: {
			addtag: {
				options: {
					tag: 'v<%= pkg.version %>',
					message: 'Version <%= pkg.version %>'
				}
			}
		},
		gitcommit: {
			commit: {
				options: {
					message: 'Version <%= pkg.version %>',
					noVerify: true,
					noStatus: false,
					allowEmpty: true
				}
			}
		},
		gitpush:{
			push: {
				options: {
					tags: true,
					remote: 'origin',
					branch: 'master'
				}
			}
		},
		replace: {
			readme_txt: {
				src: [ 'readme.txt' ],
				overwrite: true,
				replacements: [{
					from: /Stable tag: (.*)/,
					to: 'Stable tag: <%= pkg.version %>'
				}]
			},
			'plugin_file': {
				src: [ '<%= pkg.main_file %>' ],
				overwrite: true,
				replacements: [{
					from: /\*\s*Version:\s*(.*)/,
					to: '* Version: <%= pkg.version %>'
				}]
			}
		},
		svn_export: {
			dev: {
				options:{
					repository: 'https://plugins.svn.wordpress.org/<%= pkg.name %>',
					output: 'build/<%= pkg.name %>'
				}
			}
		},
		push_svn:{
			options: {
				// Put username/password here.
				remove: true
			},
			main: {
				src: 'build/<%= pkg.name %>',
				dest: 'https://plugins.svn.wordpress.org/<%= pkg.name %>',
				tmp: 'build/make_svn'
			}
		},

		// Generate POT.
		makepot: {
			target: {
				options: {
					type: 'wp-plugin',
					domainPath: 'languages',
					exclude: ['build/.*', 'deploy/.*', 'node_modules/.*'],
					updateTimestamp: false,
					potHeaders: {
						'report-msgid-bugs-to': '',
						'x-poedit-keywordslist': true,
						'language-team': '',
						'Language': 'en_US',
						'X-Poedit-SearchPath-0': '../../<%= pkg.name %>',
						'plural-forms': 'nplurals=2; plural=(n != 1);',
						'Last-Translator': 'Nilambar Sharma <nilambar@outlook.com>'
					}
				}
			}
		},

		// Check textdomain.
		checktextdomain: {
			options: {
				text_domain: '<%= options.text_domain %>',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src: [
					'**/*.php',
					'!node_modules/**',
					'!deploy/**',
					'!build/**'
				],
				expand: true
			}
		},

		// Update text domain.
		addtextdomain: {
			options: {
				textdomain: '<%= options.text_domain %>',
				updateDomains: true
			},
			target: {
				files: {
					src: [
					'*.php',
					'**/*.php',
					'!node_modules/**',
					'!deploy/**',
					'!build/**',
					'!tests/**'
					]
				}
			}
		},

		// CSS minification.
		cssmin: {
			target: {
				files: [{
					expand: true,
					cwd: '<%= dirs.css %>',
					src: ['*.css', '!*.min.css'],
					dest: '<%= dirs.css %>',
					ext: '.min.css'
				}]
			}
		},
		// Check JS.
		jshint: {
			options: grunt.file.readJSON( '.jshintrc' ),
			all: [
				'<%= pkg.main %>',
				'js/*.js',
				'!js/*.min.js'
			]
		},

		// Uglify JS.
		uglify: {
			target: {
				options: {
					mangle: false
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>',
					src: ['*.js', '!*.min.js'],
					dest: '<%= dirs.js %>',
					ext: '.min.js'
				}]
			}
		}
	});

	// Load NPM tasks to be used here.
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-git' );
	grunt.loadNpmTasks( 'grunt-text-replace' );
	grunt.loadNpmTasks( 'grunt-svn-export' );
	grunt.loadNpmTasks( 'grunt-push-svn' );

	// Register tasks.
	grunt.registerTask( 'default', [] );

	grunt.registerTask( 'build', [
		'cssmin',
		'uglify',
		'addtextdomain',
		'makepot'
	]);

	grunt.registerTask( 'precommit', [
		'jshint',
		'checktextdomain'
	]);

	grunt.registerTask( 'textdomain', [
		'addtextdomain',
		'makepot'
	]);

	grunt.registerTask( 'deploy', [
		'clean:deploy',
		'copy:deploy'
	]);

	grunt.registerTask( 'version_number', [ 'replace:readme_txt', 'replace:plugin_file' ] );
	grunt.registerTask( 'pre_vcs', [ 'version_number', 'textdomain' ] );
	grunt.registerTask( 'gitattributes', [ 'file-creator' ] );

	grunt.registerTask( 'do_svn_dry', [ 'svn_export', 'clean:remove_trunk', 'copy:svn_trunk', 'copy:svn_tag' ] );
	grunt.registerTask( 'do_svn_run', [ 'svn_export', 'clean:remove_trunk', 'copy:svn_trunk', 'copy:svn_tag', 'push_svn' ] );
	grunt.registerTask( 'do_git', [  'gitcommit', 'gittag', 'gitpush' ] );
	grunt.registerTask( 'release', [ 'pre_vcs', 'do_svn_run' ] );
	grunt.registerTask( 'post_release', [ 'do_git', 'clean:post_build' ] );
};
