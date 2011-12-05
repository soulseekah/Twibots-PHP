<?php
	class Trim140 extends Filter {
		private $max_length;

		public function __construct($max_length = 140) {
			$this->max_length = $max_length;
		}

		public function filter(Writable $writable) {
			while(strlen($this->construct($writable->title, $writable->permalink, $writable->tags)) >= $this->max_length) {
				/* Remove tags one at a time */
				if (sizeof($writable->tags)) {
					unset($writable->tags[sizeof($writable->tags - 1)]);
					continue;
				}

				/* Remove content chop off the last word */
				if (strlen($writable->title)) {
					$title = explode(' ', $writable->title);
					unset($title[sizeof($title) - 1]);
					$writable->title = implode(' ', $title);
					continue;
				}

				return false; /* Tweet is too long */
			}

			$writable->output = $this->construct($writable->title, $writable->permalink, $writable->tags);
			return $writable;
		}

		private function construct($title, $permalink, $tags = array()) {
			return $title.' '.$permalink.' '.implode(' ', $tags);
		}
	}
?>
