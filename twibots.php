<?php
	/* This is a twibot */

	/* Load up the configuration and initialize */
	require 'config.php'; require 'init.php';

	/* Parse an auth key if provided */
	$args = getopt('', array('auth:'));
	if (isset($args['auth'])) {
		if (!file_exists($args['auth'])) die('Auth file does not exist'."\n");
		$credentials = unserialize(file_get_contents($args['auth']));
		$twitter = new Twitter(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $credentials);
	} else $twitter = new Twitter(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);

	$twitter->authenticate(); /* Who are you? What do you want? */

	/* Earthlings! Hear me and hear me well! */
	$twibot = new Twitbot();
?>
