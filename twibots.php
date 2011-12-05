<?php
	/* This is a typical Twibot setup */

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

	/* Add some filters */
	$twitter->add_filter('trim140', new Trim140());

	$twibot = new Twibot(); /* Spawn the bot */

	$twibot->add_channel('twitter', $twitter);
	$twibot->add_source('themefm', new RssFeed('http://feeds.feedburner.com/themefm?format=xml'));

	/* Earthlings! Hear me and hear me well! */
	$twibot->live();
?>
