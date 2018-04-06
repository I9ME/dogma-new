var gulp = require('gulp');
var sass = require('gulp-sass');
var rename = require('gulp-rename');
var minify = require('gulp-minify');
var watch       = require('gulp-watch');
var spritesmith = require('gulp.spritesmith');
var imagemin        = require('gulp-imagemin');
var changed         = require('gulp-changed');
//var connect = require('gulp-connect-multi')();
var browserSync = require('browser-sync').create();
var browsersup      = [
    'Android >= 2.3',
    'BlackBerry >= 7',
    'Chrome >= 9',
    'Firefox >= 4',
    'Explorer >= 9',
    'iOS >= 5',
    'Opera >= 11',
    'Safari >= 5',
    'ChromeAndroid >= 9',
    'FirefoxAndroid >= 4',
    'ExplorerMobile >= 9'
];

/*
 * Variables
 */
 var server = 'http://localhost/';
 var project = 'lojas';
// Sass Source
var scssPath = './_src/scss';
var scssFile = './_src/scss/main.scss';

// CSS destination
var cssDest = './assets/css';

// Options for development
var sassDevOptions = {
  outputStyle: 'expanded'
}

// Options for production
var sassProdOptions = {
  outputStyle: 'compressed'
}

//Diretorios
path = {
    dev     : './_src',
    prod    : './assets',
    proxy   : server + project,
}

/*
 * Tasks
 */

 //BrowserSync
gulp.task('sinc', function() {
  browserSync.init({
      //server: {
      //baseDir: "./"
      //},
      proxy: path.proxy,
      options: {
        reloadDelay: 250
      },
      notify: true,
  });
});




gulp.task('sprite', function () {
    var spriteData = gulp.src('./_src/images/sprites/*.png')
        .pipe(spritesmith({
            /* this whole image path is used within the css background declaration */
            imgName: '../images/sprite.png',
            cssName: '_sprites.scss',
            //cssTemplate: './_src/scss/scss.template.handlebars'  // using whatever file location you used
        }));
    spriteData.img.pipe(gulp.dest('assets/images'));
    spriteData.css.pipe(gulp.dest('./_src/scss/skeletonscss/images/includes'));
});


// Task 'sassdev' - Run with command 'gulp sassdev'
gulp.task('sassdev', function() {
  return gulp.src(scssFile)
    .pipe(sass(sassDevOptions).on('error', sass.logError))
    .pipe(gulp.dest(cssDest))
    .pipe(browserSync.reload({stream: true}));
});

// Task 'sassprod' - Run with command 'gulp sassprod'
gulp.task('sassprod', function() {
  return gulp.src(scssFile)
    .pipe(sass(sassProdOptions).on('error', sass.logError))
    .pipe(rename('main.min.css'))
    .pipe(gulp.dest(cssDest))
    .pipe(browserSync.reload({stream: true}));
});

//Task copy editor-style
gulp.task('editorstyle', function(){
  return gulp.src( scssPath + '/editor-style.scss')
  	.pipe(sass(sassDevOptions).on('error', sass.logError))
    .pipe(gulp.dest(cssDest))
    .pipe(browserSync.reload({stream: true}));
});

//Task copy fonts
gulp.task('fonts', function(){
  return gulp.src( './_src/fonts/**/*')
    .pipe(gulp.dest('./assets/fonts'));
});

// Task Minify JS
gulp.task('scripts', function() {
  gulp.src('./_src/js/*.js')
    .pipe(minify({
        ext:{
            src:'.debug.js',
            min:'.min.js'
        },
        exclude: ['tasks'],
        ignoreFiles: ['.combo.js', '-min.js']
    }))
    .pipe(gulp.dest('./assets/js'))
    .pipe(browserSync.reload({stream: true}));
});


/*gulp.task('server', connect.server({
  //root: [''],
  port: 1337,
  livereload: true,
  open: { browser: 'chrome' }
}));*/

//Imagens minificadas
gulp.task('images', function() {
    return gulp.src('./_src/images/*.png')
        .pipe(changed( './assets/images')).pipe(
            imagemin({
                optimizationLevel: 5,
                progressive: true,
                interlaced: true,
                svgoPlugins: [{removeViewBox: false}]
                }))
        .pipe(gulp.dest( './assets/images'));
});


// Task 'watch' - Run with command 'gulp watch'
gulp.task('watch', function() {
  gulp.watch('*.php').on('change', browserSync.reload);
  gulp.watch(['./_src/images/sprites/*.png'], ['sprite']);
  gulp.watch(scssFile, ['sassdev', 'sassprod', 'editorstyle' ]);
  gulp.watch('./_src/fonts/', [ 'fonts' ]);
  gulp.watch('./_src/js/*.js', [ 'scripts' ]);
  gulp.watch( './_src/images/*.png', ['images']);
});



// Default task - Run with command 'gulp'
gulp.task('default', ['sinc', 'sprite', 'sassdev', 'sassprod', 'editorstyle', 'fonts', 'scripts', 'images',  'watch' ]);


