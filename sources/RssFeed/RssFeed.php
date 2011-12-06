<?php
	require 'FeedParser.php';

	class RssFeed extends Source {
		private $feed_url;
		private $count;
		private $actions;
		private $cache;
		private $items;

		private $feed;

		public function __construct($feed_url, $count = 5, $actions = array('default')) {
			$this->feed_url = $feed_url;
			$this->count = $count;
			$this->actions = $actions;
			$this->cache = array();

			$this->init();
		}

		public function read() {
			$item = $this->get_item();

			$writable = new Writable(array(
					'title' => $item['title'],
					'permalink' => $item['permalink'],
					'tags' => $item['terms'],
					'actions' => $this->actions
				));

			return $writable; /* Fire! */
		}

		private function init() {
			echo 'Reading RSS Feed: '.$this->feed_url."\n";

			/* Read the XML */
			$this->feed = new FeedParser($this->feed_url);
		}

		private function get_item() {
			if (get_class($this->feed) != 'FeedParser') die('RSS feed parser not initialized'."\n");

			return $this->feed->get_item();
		}
	}

/*
	def read(self):
		"""
			This method is called by a Twibot. It parses the feed
			entries, converts them into writables and yields the
			results one by one.
		"""

		# Clear cache
		if len(self.cache) > 20:
			self.cache = self.cache[-10:]

		logging.debug("Reading RSS Feed: %s" % self.feed_url)
		try:
			feed = feedparser.parse(self.feed_url)
		except LookupError:
			logging.error("Cannot parse feed: %s, LookupError" % self.feed_url)
			raise StopIteration

		if not len(feed['entries']):
			logging.error("There's something wrong with the feed: %s" % self.feed_url)
			raise StopIteration

		feed['entries'] = reversed(feed['entries'][:self.count])

		for item in feed['entries']:

			if item.title in self.cache:
				continue

			self.cache.append(item.title)

			tags = []
			try:
				for tag in item.tags:
					tags.append(tag['term'])
			except:
				pass

			writable = tb.Writable(
				title=item.title,
				permalink=item.link,
				tags=tags
			)

			writable.actions = self.actions
			if 'summary' in item: writable.summary=item.summary
			if 'content' in item: writable.content=item.content[0].value

			yield writable */
?>
