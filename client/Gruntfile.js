var config = {
    scss:{
        src: 'scss',
        dest:'public/css'
    },
    js:{
        src: 'js',
        dest:'public/js'
    },
    vendor:{
        src: 'bower_components',
        dest: 'public/vendor'
    },
    prod:{
        tasks: ['sass', 'uglify', 'copy:vendors'],
        enable: true
    },
    dev:{
        tasks: ['sass', 'copy:js'],
        enable: true
    }
};





module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            dist: {
                files: [{
                    expand: true,
                    cwd: config.scss.src,
                    src: ['**/*.scss'],
                    dest: config.scss.dest,
                    ext: '.css'
                }]
            }
        },
        uglify: {
            dist: {
                files: [{
                    expand: true,
                    cwd: config.js.src,
                    src: '**/*.js',
                    dest: config.js.dest
                }]
            }
        },
        copy: {
            vendors: {
                files: [{
                    cwd: config.vendor.src,
                    expand: true,
                    src: ['**'],
                    dest: config.vendor.dest
                }]
            },
            js: {
                files: [{
                    cwd: config.js.src,
                    expand: true,
                    src: ['**'],
                    dest: config.js.dest
                }]
            }
        },
        watch: {
            css: {
                files: config.scss.src+'**/*.scss',
                tasks: ['sass']
            },
            js:{
                files: config.js.src+'/**/*.js',
                tasks: ['copy:js']
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    if (grunt.option('prod')) {
        if (config.prod.enable){
            grunt.task.run();
        }

    } else {
        if ( config.dev.enable ){
            grunt.registerTask('default',['watch']);
            grunt.task.run(config.dev.tasks);
        }
    }
}