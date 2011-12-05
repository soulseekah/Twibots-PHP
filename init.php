<?php
	/* Load up the necessary core classes */
	require APPDIR.'core/Twibot.php';
	require APPDIR.'core/Source.php';
	require APPDIR.'core/Channel.php';
	require APPDIR.'core/Filter.php';

	/* Load up the necessary sources */
	require APPDIR.'sources/RssFeed.php';

	/* Load up the necessary channels */
	require APPDIR.'channels/twitter/Twitter.php';

	/* Load up the necessary filters */
	require APPDIR.'filters/Trim140.php';
?>
