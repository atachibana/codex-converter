/**
 *  Codex Translator Aid requester functions.
 *
 * @summary   Requests to Codex Translater Aid via Ajax
 *
 * @requires  JQuery
 * @link      https://github.com/atachibana/codex-converter/
 * @author    Akira Tachibana
 */

// Where Ajax request is sent. Depending on the environment, change the
// absolute path.
// var TARGET_URL = 'ws-codex-converter.php';
var TARGET_URL = '/ws-codex-converter/ws-codex-converter.php';

/**
 * @summary   When page was loaded, those functions are called.
 */
$( document ).ready( function() {
  clearControls();
  initialTextSet();

  /**
   * @summary  handles Ajax communication.
   *
   * When 'migrate' button was clicked, remote TARGET_URL is called via Ajax.
   *
   * @return boolean returns always false to inhibit page loading.
   */
  $( '#migrate' ).click( function() {
    $( "#loading" ).html( '<img src="loader.gif" />' );
    clearControls();
    var data = { codex : $('#codex').val(), converter_type : 'JaCodex' };
      $( '#convertd-text' ).val( '' );
 			$.ajax({
        type: 'POST',
 				url: TARGET_URL,
 				data: data,
 			}).done(function( data, dataType ) {
        $( '#convertd-text' ).val( data );
        $( '#convertd-text').focus();
      }).fail( function( XMLHttpRequest, textStatus, errorThrown ) {
        var msg = 'XMLHttpRequest : ' + XMLHttpRequest.status + ', '
                + 'textStatus : ' + textStatus + ', '
                + 'errorThrown : ' + errorThrown.message;
        $( '#msg-area' ).html( msg );
      }).always( function( arg1, status, arg2 ) {
        $( '#loading' ).html( '' );
      });
 			return false;     // Do not reloead the page.
 		});
 });

/**
 * @summary   Sets initial instructional text into the textarea.
 */
function initialTextSet() {
  $( '#codex' ).val( '==Demo==\n\
Click right button and see the results.\n\
==Description==\n\
This program converts English codex contents to specfied language style.\n\
Not machine translation program, though major titles and some values are translated.\n\
==How to use==\n\
# Cut & paste Codex source here.\n\
# Click right button\n\
# Check whole contents.\n\
==Reference==\n\
[http://wpdocs.osdn.jp/%E3%83%98%E3%83%AB%E3%83%97:%E7%B7%A8%E9%9B%86%E6%A1%88%E5%86%85]\n\
==Comments==\n\
Send the mail to atachibana<at>unofficialtokyo.com. Any comments are welcome.');
}

/**
 * @summary   Clears output area and error message area.
 */
function clearControls() {
  $( '#convertd-text' ).val( '' );
  $( '#msg-area' ).html( '<br />' );  // to keep the 1-line, inserts break.
  return;
}
