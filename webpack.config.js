const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
  ...defaultConfig,
  entry: {
    index: path.resolve(__dirname, 'assets/src', 'index.js'),
  },
  output: {
    path: path.resolve(__dirname, 'assets/build'),
    filename: 'movie-block.min.js',
  }
};