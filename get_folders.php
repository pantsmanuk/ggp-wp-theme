<?php 
	include_once('functions.php');
	$path = '../../downloads/';
	$folder = (empty($_REQUEST['folder']) || $_REQUEST['folder'] == 'undefined') ? '' : $_REQUEST['folder'];
?>
<script language="javascript">
	folders = '<?php echo getDownloads($path, $folder); ?>';
	if (folders == '<ul></ul>') {
		folders = '<ul><li>Folder contains no files to download</li></ul>';
	}
	var docbrowser = new Element('div', {
		'id': 'document_browser',
	    'html': folders
	});
	if ($('document_browser')) {
		$('document_browser').set('html', folders);
	} else {
		docbrowser.inject($('article'), 'after');
	}
</script>