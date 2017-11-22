/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

const Sass = require('node-sass');
const CssNodeExtract = require('css-node-extract');
const PostCssScss = require('postcss-scss');
const FileSystem = require('fs');
const Path = require('path');
const Glob = require('glob');

let configuration = {
  root: Path.resolve(__dirname, '../../../../../'),
  source: 'Resources/Private/Sass/**/*.scss',
  target: 'Resources/Public/Css',
  include: [
    'Build/node_modules/bootstrap-sass/assets/stylesheets',
    'Build/node_modules/font-awesome/scss',
    'Build/node_modules/eonasdan-bootstrap-datetimepicker/src/sass',
    'Build/node_modules/tagsort',
    'Build/Resources/Public/Sass'
  ],
  bootstrap: 'Build/Resources/Public/Sass/backend.scss'
};
let includes = [];

process.argv.slice(2).forEach(value => {
  if (value.startsWith("--")) {
    let option = value.substring(2).split("=");
    configuration[option[0]] = JSON.parse(option[1]);
  }
});

configuration.include = configuration.include.map(include => Path.resolve(configuration.root, include));

Promise.all(
  [
    new Promise(
      (resolve, reject) => FileSystem.readFile(
        Path.resolve(configuration.root, configuration.bootstrap),
        'utf8',
        (error, result) => error ? reject(error) : resolve(result)
      )
    ).then(
      data => new Promise((resolve, reject) => Sass.render({
        importer: (url, previous, done) => {
          url = url.replace(/([^\/]+)(?!.scss)$/, '_$1.scss');
          let path = configuration.include.filter(path => FileSystem.existsSync(Path.resolve(path, url))).shift();

          if (path) {
            includes.push(Path.resolve(path, url));
          }
          return null;
        },
        includePaths: configuration.include,
        data: data
      }, (error, result) => error ? reject(error.formatted) : resolve(includes)))
    ).then(
      data => Promise.all(data.map(file => new Promise((resolve, reject) => FileSystem.readFile(
        file,
        'utf8',
        (error, result) => error ? reject(error) : resolve(result)
      ))))
    ).then(
      data => CssNodeExtract.process({
        css: data.join('\n'),
        filterNames: ['variables'],
        postcssSyntax: PostCssScss
      })
    ).then(
      data => data + ';'
    )
  ].concat(
    Glob.sync(configuration.source, {nonull: false, cwd: Path.resolve(__dirname, '../../')}).map(
      file => new Promise((resolve, reject) => FileSystem.readFile(
        Path.resolve(__dirname, '../../', file),
        'utf8',
        (error, data) => error ? reject(error) : resolve(data)
      ))
    )
  )
).then(
  data => new Promise((resolve, reject) => Sass.render({
    includePaths: configuration.include,
    data: data.join('\n'),
    outputStyle: 'expanded',
    precision: 8
  }, (error, result) => error ? reject(error.formatted) : FileSystem.writeFile(
    Path.resolve(__dirname, '../../', configuration.target) + '/grid.css',
    result.css.toString(),
    error => error ? reject(error) : resolve(result)
  )))
).catch(
  error => {
    console.error(error);
    process.exit(1);
  }
);
