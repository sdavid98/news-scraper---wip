"use strict";

module.exports = function (grunt) {
    const sass = require('node-sass');
    // Load all Grunt tasks that are listed in package.json automagically
    require("load-grunt-tasks")(grunt);
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        sass: {
            options: {
                implementation: sass,
                sourceMap: true,
                style: 'compressed'
            },
            dist: {
                files: {
                    "web/css/style.css": "src/scss/style.scss"
                }
            }
        },
        concat: {
            options: {
                separator: ';',
            },
            dist: {
                files: {
                    'web/js/build.js': 'src/js/custom/*.js',
                    'web/js/jquery.js': 'src/js/vendor/jquery/*.js',
                    'web/js/popper.js': 'src/js/vendor/popper/*.js',
                    'web/js/bootstrap.js': 'src/js/vendor/bootstrap/*.js'
                }
            },
        },
        uglify: {
            dist: {
                files: {
                    'web/js/build.min.js': 'dist/js/build.js'
                }
            }
        },
        sprite: {
            all: {
                src: 'src/icons/*.{png,jpg,jpeg,gif}',
                dest: 'web/images/sprites.png',
                destCss: 'src/scss/components/_sprites.scss',
                cssTemplate: 'src/icons/scss.template.mustache',
                imgPath: '/images/sprites.png'
            }
        },
        imagemin: {
            dynamic: {
                files: [{
                    expand: true,
                    cwd: 'src/images',
                    src: ['*.{png,jpg,gif}'],
                    dest: 'web/images/'
                }]
            }
        },
        autoprefixer: {
            options: {
                map: true
            },
            dist: {
                files: {
                    'web/css/style.css': 'dist/css/style.css'
                }
            }
        },
        watch: {
            sass: {
                files: "src/scss/**/*.scss",
                tasks: ["sass"]
            },
            concat: {
                files: "src/js/**/*.js",
                tasks: ["concat"]
            },
            spritesmith: {
                files: 'src/icons/*.{png,jpg,jpeg,gif}',
                tasks: ['sprite']
            },
            imagemin: {
                files: 'src/images/*.{png,jpg,gif}',
                tasks: ['imagemin']
            }
        },
        browserSync: {
            dev: {
                bsFiles: {
                    src: ["web/**/*.*"]
                },
                options: {
                    watchTask: true,
                    proxy: "ocuvane.local"
                }
            }
        }
    });
    // Custom tasks
    grunt.registerTask('default', ['browserSync', 'watch']);
};
