<?php
/**
 * Template Name: Codex Converter to HelpHub
 *
 * Page template of Codex Converter page. It is assuming child theme of
 * Twenty-Sixteen.
 * With this page template, instantiate the document with the title you want
 * show. If you need just pure html page for Codex conversion (i:e out of
 * WordPress), then use codextohelphub.html or command line interface
 * command-codextohelphub.php
 *
 * @link		https://github.com/atachibana/codex-converter/
 * @author		Akira Tachibana
 */
get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<header class="entry-header">
	<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
</header><!-- .entry-header -->

<div id="codex-converter">
	<p id="msg-area"><br /></p>

	<div id="codex-converter-main">
  		<div class="sub-panel">
    		<textarea class="codetext" id="codex"
    			placeholder="Cut &amp; Paste Codex source here and click the button.">
			</textarea>
  		</div> <!-- .sub-panel -->

		<div id="center-panel">
			<input type="button" id="migrate" value=" >> " /><br />
			<span id="loading"></span>
		</div> <!-- .center-panel -->

  		<div class="sub-panel">
    		<textarea class="codetext" id="convertd-text"></textarea>
  		</div> <!-- .sub-panel -->

	</div> <!-- .codex-converter-main -->
</div> <!-- .codex-converter --->
</article>

<hr />

<?php get_footer(); ?>
