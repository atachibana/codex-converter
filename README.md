# codex-converter

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
codex-converter uses own Logger class only for a removal of external library dependency. That has the same interface with log4php with some limitation.

* Not ussing log-config.xml. To change log file name or log level, edit class-logger.php direct.
* Terrible performance when LOGGERLEVEL_TRACE is set for long Codex article. Use only for debug or consider to use log4php.

## log4php
You should use log4php if it is possible.

To use log4php
1. replace require_once in class-codex.php
2. rename sample-log-config.xml to log-config.xml


# Limitation

Codex to HelpHub:
* `<pre>` tag or line begin with space character is always converted to `[code language="php"]`.

Codex Translater Aid Tool (Beta):
* Japanse support only.
* Some functions are not implemented yet such as automatic retrieve function by URL specification. Refer ToDo.

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

# Customization of Converter

If you want to create your own converter, follow below steps:
1. Define your converter symbol (ex. KoCodex)
2. Create subdirectory and class file with lower characters of converter symbol (ex. kocodex/class-kocodex-converter.php)
3. Implements interface-converter.php. Refer helphub/ or jacodex/. (ex. KoCodexConverter class)
4. Instantiate Codex class with the converter symbol.

```
$codex_to = new Codex( 'KoCodex' );
$out_array = $codex_to->convert( $in_array );

```

# ToDo

## Codex Translator Aid tool

* URL input support. It will get the contents and last modifier information.
* Transferred DevHub page support. In this case, it will get last - 1 revision from Codex and inform it translator.
* Other language support as sample.

# Author
Akira Tachibana (http://unofficialtokyo.com)

# History
## Version 1.0 (3/Jul/2016)
* Release of Codex to Help Hub
* Beta of Codex Translator Aid

## Version 0.1 (24/Jun/2016)
* Early drop
