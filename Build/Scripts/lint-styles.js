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

const StyleLint = require('stylelint');
const Path = require('path');

let configuration = {
  root: Path.resolve(__dirname, '../../../../../'),
  source: 'Resources/Private/Sass/**/*.scss'
};

process.argv.slice(2).forEach(value => {
  if (value.startsWith("--")) {
    let option = value.substring(2).split("=");
    configuration[option[0]] = JSON.parse(option[1]);
  }
});

StyleLint.lint({
  formatter: 'verbose',
  configFile: Path.resolve(configuration.root, '.stylelintrc'),
  files: Path.resolve(__dirname, '../../', configuration.source)
}).then(
  data => {
    console.info(data.output)
  }
).catch(
  error => {
    console.error(error);
    process.exit(1);
  }
);
