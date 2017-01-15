# codex-converter
[![Build Status](https://travis-ci.org/atachibana/codex-converter.svg?branch=master)](https://travis-ci.org/atachibana/codex-converter)
The codex-converter is aid tool for WordPress Codex migrator or translater. It automatically converts Codex article written by MediaWiki format to specified format.

For example, Codex article

```
==Title==
This is example article.
* List1
* List2
```

will be converted to HelpHub article by Codex to HelpHub converter.

```
<h2>Title</h2>
<p>This is example article.</p>
<ul>
<li>List1</li>
<li>List2</li>
</ul>
```

Current codex-converter package includes two type of converter:

* HelpHub
* Japanese Codex (Beta)

# Usage
## Online
Codex to HelpHub: <br />http://unofficialtokyo.com/codex-converter

Codex Translator Aid Tool  (Beta): <br />
http://unofficialtokyo.com/codex-translater-aid

1. Click center button for demo.
2. Cut and Paste the Codex text into the left box and click center button.
3. Refer "TODO" that requires post production (ex. Image paste).
4. Code is always assumed as PHP. Adjust the line [code lang=php].
5. Check whole contents. This program is not complete converter.

## Command line
You can convert on your local computer.

Codex to HelpHub:

```
php command-codextohelphub.php -i <input_file> -o <output_file>
```

# Log
Refer codex-converter.log. To change log file name or log level, edit class-logger.php direct.

## logger
codex-converter uses own Logger class only for a removal of external library dependency. That has the same interface with log4php with limitations.

* Not ussing log-config.xml. To change log file name or log level, edit class-logger.php direct.
* Terrible performance when LOGGERLEVEL_TRACE is set for long Codex article. Use only for debug or consider to use log4php.

## log4php
You should use log4php if it is possible.

To use log4php
1. replace require_once in class-codex.php
2. rename sample-log-config.xml to log-config.xml


# Restriction

Codex to HelpHub:
* `<pre>` tag or line begin with space character is always converted to `[code language="php"]`.

Codex Translater Aid Tool (Beta):
* Japanse support only.
* Some functions are not implemented yet such as automatic retrieve function by URL specification. Refer ToDo.

# Files

```
README.md                         readme (This file)
testrun.bat                       (Windows) phpunit test kicker
run.bat                           (Windows) command line HelpHub launcher

src/
  class-codex.php                 Codex class (main)
  interface-converter.php         Interface of Converter
  ws-codex-converter.php          receiver
  class-logger.php                Own poor Logger
  class-result.php                Output result keeper            
  class-util.php                  Utility handles line type
  codex-converter.css             stylesheet
  loader.gif                      animated loader
  sample-functions.php            sample functions.php (part of)     
  sample-log-config.xml           sample log4php configuration file
  (codex-converter.log)           (not included) log file

                                  // --- Codex to HelpHub ---
  page-codextohelphub.php         Page Template
  codex-converter.js              invoker
  codextohelphub.html             standalone web page (test purpose)
  command-codextohelphub.php      command line interface
  helphub
    class-helphub-converter.php   Converter class (main logic)

                                  // ---Codex Translator Aid ---
  page-codextranslatoraid.php     Page Template
  codex-translator-aid.js         invoker
  codextranslatoraid.html         standalone web page (test purpose)
  jacodex
    class-jacodex-converter.php   Converter class (main logic)

tests/
  CodexHelpHubTest.php            phpunit test for Codex to HelpHub
  CodexJaCodexTest.php            phpunit test for Codex Translator Aid
  (others)                        test files
  (others_expected)               expected results
```


# Deployment

Codex to HelpHub Deployment steps are as following. Codex Translator Aid tool is the same except file name.

## Web Application

1. Copy every files under the codex-converter/src including sub-directory to Web Server. In this scenario, let's assume files are copied to  htdocs/ws-codex-converter.
2. Edit `TARGET_URL` in `codex-converter.js` to point the ws-codex-converter.php. For example, it is `var TARGET_URL = 'ws-codex-converter.php';` or `var TARGET_URL = '/ws-codex-converter/ws-codex-converter.php';`
3. Access http://(your-host)/ws-codex-converter/codextohelphub.html

## Custom Page Template

1. Even Custom Page Template, first try above "Web Application" step for confirmation.<br /><strong>NOTE</strong>: Specify absolute path to `TARGET_URL` in `codex-converter.js` as the first step, if you get error "XMLHttpRequest : 404, textStatus : error, errorThrown : undefined".
2. Copy following files to your theme directory
<ul>
<li>page-codextohelphub.php</li>
<li>sample-functions.php</li>
<li>codex-converter.css</li>
<li>codex-converter.js</li>
<li>loader.gif</li>
</ul>
3. Append contents of `sample-functions.php` into `functions.php`.
4. Edit `TARGET_URL` in `codex-converter.js`. Refer above NOTE in Step 1.
5. Create the Page with page template "Codex Converter to HelpHub". Just title is enough(i:e no contnents are required in page). Publish it.
6. Access that page.

# Test
It assumes phpunit can be invoked.
## Runs all test at once
* testrun

## Runs specific test
* testrun test01 (or test02, test03, ...)

For test case details, refer tests/CodexHelpHubTest.php.

# Customization of Converter

If you want to create your own converter, follow below steps:
1. Define your converter symbol (ex. KoCodex)
2. Create subdirectory and class file with lower characters of converter symbol (ex. kocodex/class-kocodex-converter.php)
3. Implements interface-converter.php. Refer helphub/ or jacodex/. (ex. KoCodexConverter class)
4. Instantiate Codex class with the converter symbol.

```
$codex_to = new Codex( 'KoCodex' );
$output_data = $codex_to->convert( $input_data );

```

# ToDo

## Codex Translator Aid tool

* URL input support. It will get the contents and last modifier information.
* Transferred DevHub page support. In this case, it will get last - 1 revision from Codex and inform it translator.
* Other language support as sample.

# Author
Akira Tachibana (http://unofficialtokyo.com)

# History
## Version 1.2 (24/Dec/2016)
* Add span id to h1 - h2 tag
* Fix: Link included space and special characters
* Fix: No \_\_TOC\_\_

## Version 1.1 (24/Jul/2016)
* `convert()` handles string input/output.
* Line types handling routines were moved to new `Util` class.
* `TESTRUN` takes filter options.
* Fix: `**` sub list support (#9)
* Fix: multiple spaces only lines are not converted to code anymore.

## Version 1.0.1 (4/Jul/2016)
* &lt;nowiki> support
* fixed Windows batch file extension

## Version 1.0 (3/Jul/2016)
* Release of Codex to Help Hub
* Beta of Codex Translator Aid

## Version 0.1 (24/Jun/2016)
* Early drop
