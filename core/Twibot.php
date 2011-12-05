<?php
	/* I am a Twibot */
	class Twibot {
		private $sources;
		private $channels;
		private $filters;

		public function __construct($name = '') {
			$this->name = $name;
		}

		/* Getters and setters */
		public function add_source(Source $source) {
		}

		public function add_channel(Channel $channel) {
		}

		public function add_filter(Filter $filter) {
		}

		public function remove_source($id) {
		}

		public function remove_channel($id) {
		}

		public function remove_filter($id) {
		}
	}
?>
