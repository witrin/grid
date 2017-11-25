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
  linter: '../node_modules/tslint/bin/tslint'
};

process.argv.slice(2).forEach(value => {
  if (value.startsWith("--")) {
    let option = value.substring(2).split("=");
    configuration[option[0]] = JSON.parse(option[1]);
  }
});

const linter = ChildProcess.spawn(
  Path.resolve(__dirname, configuration.linter),
  [
    '-c',
    Path.resolve(configuration.root, 'Build/tslint.json'),
    Path.resolve(__dirname, '../../Resources/Private/TypeScript/**/*.ts')
  ]
);

linter.stdout.on('data', (data) => {
  console.log(data.toString());
});

linter.stderr.on('data', (data) => {
  console.error(data.toString());
});