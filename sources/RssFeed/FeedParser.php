<?php
	/* Uses SimpleXML to parse Feeds */
	class FeedParser {
		private $xml;
		private $channel;
		private $items;
		private $namespaces;

		public function __construct($feed) {
			/* Todo: detect type and possible namespaces before parsing */

			if (strpos($feed, 'http://') == 0) $this->parse_url($feed);
			else $this->parse_string($feed);

			/* Handle possible namespaces, etc. */
			$this->namespaces = $this->xml->getDocNamespaces();

			/* Get all the items together */
			foreach ($this->xml->channel->item as $item) $this->items []= $this->map($item);
			/* Get all the channel info in */
			$this->channel = $this->channel($this->xml->channel);
		}

		/* API */
		public function get_item() {
			return array_shift($this->items);
		}

		public function get_channel() {
			return $this->channel;
		}

		/* Helpers */
		private function parse_url($xml_url) {
			$xml = file_get_contents($xml_url);
			$this->xml = $this->_parse($xml);
		}

		private function parse_string($xml) {
			$this->xml = $this->_parse($xml);
		}

		private function _parse($xml) {
			return simplexml_load_string($xml, null);
		}

		private function map($_item /*, type */) {
			$item = array();

			$item['title'] = (string)$_item->title;
			$item['permalink'] = (string)$_item->link;
			$item['author'] = (string)$_item->children($this->namespaces['dc'])->creator;
			$item['terms'] = array();
			foreach ($_item->category as $category) $item['terms'] []= (string)$category;

			return $item;
		}

		private function channel($_channel /*, type */) {
			$channel = array();

			$channel['title'] = (string)$_channel->title;
			$channel['permalink'] = (string)$_channel->link;
			$channel['description'] = (string)$_channel->description;

			return $channel;
		}
	}
?>
