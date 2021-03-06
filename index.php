<?php
/**
 * Run PHP Code
 * 
 * This script gives you the ability to quickly test snippets of PHP code locally.
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://github.com/websiteduck/Run-PHP-Code Run PHP Code
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) die('dead');

define('NL', PHP_EOL);

if (isset($_POST['phprun_action']) && $_POST['phprun_action'] == 'download') {
	if (substr($_POST['phprun_filename'], -4) !== '.php') $_POST['phprun_filename'] .= '.php';
	header('Content-Type: text/plain');
	header('Content-Disposition: attachment; filename=' . $_POST['phprun_filename']);
	echo $_POST['phprun_code'];
	die();
}

if (isset($_POST['phprun_action']) && $_POST['phprun_action'] == 'run') {
	header('Expires: Mon, 16 Apr 2012 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); 
	header('Cache-Control: no-store, no-cache, must-revalidate'); 
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
	header('X-XSS-Protection: 0');
	ini_set('display_errors', 1);
	switch ($_POST['error_reporting'])
	{
		case 'fatal': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR); break;
		case 'warning': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_WARNING); break;
		case 'deprecated': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_WARNING | E_DEPRECATED | E_USER_DEPRECATED); break;
		case 'notice': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_WARNING | E_DEPRECATED | E_USER_DEPRECATED | E_NOTICE); break;
		case 'all': error_reporting(-1); break;
		case 'none': default: error_reporting(0); break;
	}
	$phprun_code = '?>' . ltrim($_POST['phprun_code']);
	ob_start();
	eval($phprun_code);
	$phprun_html = ob_get_clean();
	if (isset($_POST['pre_wrap'])) $phprun_html = '<pre>' . $phprun_html . '</pre>';
	if (isset($_POST['colorize'])) $phprun_html = '<link rel="stylesheet" href="css/colorize.css">' . $phprun_html;
	echo $phprun_html;
	die();
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Run PHP Code</title>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="js/ace/ace.js" charset="utf-8"></script>
		<script type="text/javascript" src="js/run_php_code.js"></script>

		<link rel="shortcut icon" href="favicon.ico" >
		<link rel="stylesheet" href="css/run_php_code.css">
	</head>
	<body>
		<img id="resize_ball" src="img/resize_ball.png" />
		
		<form id="run_php_form" method="POST" action="" target="run_php_code" onsubmit="run_php_form_submit()">
			<input type="hidden" name="phprun_action" value="run" />
			<input type="hidden" name="phprun_filename" value="" />
			<div id="title_bar">
				<div id="title">Run PHP Code</div>
				
				<div class="drop"><span>File</span>
					<div>
						<button class="button" id="btn_import" type="button">Remote Import...</button>
						<button class="button" id="btn_save" type="button">Save...</button>
					</div>
				</div>
				
				<div class="drop"><span>Options</span>
					<div>
						<input type="checkbox" id="colorize" name="colorize" checked="checked" /><label for="colorize"><span></span> Colorize</label><br />
						<input type="checkbox" id="external_window" /><label for="external_window"><span></span> External Window</label><br />
						<input type="checkbox" id="pre_wrap" name="pre_wrap" /><label for="pre_wrap"><span></span> &lt;pre&gt;</label><br />
					</div>
				</div>
					
				<div id="button_container">
					<label>
						Error Reporting
						<select name="error_reporting">
							<option value="none">None</option>
							<option value="fatal" selected="selected">Fatal</option>
							<option value="warning">Warning</option>
							<option value="deprecated">Deprecated</option>
							<option value="notice">Notice</option>
							<option value="all">All</option>
						</select>
					</label>
					<button class="button" type="button" id="reset"><img src="img/clear.png" class="icon" /> Clear</button>
					<button class="button" type="submit" id="run" title="Run (Ctrl+Enter)">Run <img src="img/run.png" class="icon" /></button>
					
					<div class="drop">
						<img id="help" src="img/help.png" style="" />
						<div id="help_window">
							<h2>Run PHP Code</h2>
							<p>Ctrl-Enter to Run Code</p>
							
							<p>
								<img src="img/website_duck.png" alt="" style="width: 50px; height: 50px;" /><br />
								Website Duck LLC<br />
								<a class="button" href="https://github.com/websiteduck/Run-PHP-Code">GitHub Repo</a>
							</p>
						</div>
					</div>
				</div>
			</div>
			
			<div id="php"></div>
			<input type="hidden" id="phprun_code" name="phprun_code" />
		</form>
		
		<iframe id="php_frame" name="run_php_code">
		</iframe>
		
	</body>
</html>