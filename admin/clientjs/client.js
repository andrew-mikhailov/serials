var fs = require('fs'),
  chokidar = require('chokidar'),
  mkdirp = require('mkdirp'),
  spawn = require('child_process').spawn;

mkdirp('./files/');

// Get config
var config = fs.readFileSync('config.json');
config = JSON.parse(config);

// One-liner for current directory, ignores .dotfiles
var watcher = chokidar.watch('./files');
var log = console.log.bind(console);

watcher.on('change', uploadFile );
watcher.on('add', uploadFile);

function uploadFile (path) {
  log('File ' + path + ' has been added');
  // We need to check file name (TVShow with spaces.s1.e1.480.title with spaces.mp4)
  // ([\w\d\s]+) - TVShow title
  // (s\d+) - season number
  // (e\d+) - episode number
  // (\d+) - quality
  // ([\w\d\s]+) - episode title
  // (mp4) - file extensions
  var regexr = /([\w\d\s]+).(s\d+).(e\d+).(\d+).([\w\d\s]+).(mp4)/;
  path = path.split('files/');
  path.shift();
  var filename = path.shift();
  if (regexr.test(filename)) {
    log('File ' + filename + ' is uploading');

    var url = 'http://' + config.basicAuth.username + ':'
      + config.basicAuth.password + '@'
      + config.domain + ':' + config.port;

    filename = './files/' + filename;
    var child = spawn('curl', ['--upload-file', filename, url]);

    // Send data to the child process via its stdin stream
    // Listen for any response from the child:
    child.stdout.on('data', function (data) {
      console.log('We received: file ' + filename  + ' has a status: '+ data);
    });
  }
}
