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
const ChildProcess = require('child_process');
const Path = require('path');
const FileSystemExtra = require('fs-extra');

let configuration = {
  root: Path.resolve(__dirname, '../../../../../'),
  compiler: '../node_modules/typescript/bin/tsc'
};

process.argv.slice(2).forEach(value => {
  if (value.startsWith("--")) {
    let option = value.substring(2).split("=");
    configuration[option[0]] = JSON.parse(option[1]);
  }
});

const compiler = ChildProcess.spawn(
  Path.resolve(__dirname, configuration.compiler),
  [
    '--project',
    Path.resolve(__dirname, '../tsconfig.json')
  ]
);

compiler.stdout.on('data', (data) => {
  console.log(data.toString());
});

compiler.stderr.on('data', (data) => {
  console.error(data.toString());
});

compiler.on('exit', (code, signal) => {
  FileSystemExtra.copy(
    Path.resolve(__dirname, '../JavaScript/typo3conf/ext/grid/Resources/Private/TypeScript/'),
    Path.resolve(__dirname, '../../Resources/Public/JavaScript/'),
    error => error ? console.error(error) : FileSystemExtra.remove(
      Path.resolve(__dirname, '../JavaScript/'),
      error => error && console.error(error)
    )
  )
});