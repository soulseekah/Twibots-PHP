<?php
	/* I am a Twibot */
	class Twibot {
		private $sources;
		private $channels;

		public function __construct($name = '') {
			$this->name = $name;
		}

		public function live() {
			/* A bit loop */
			while (true) {
				foreach ($this->sources as $source) {
					$writable = $source->read();
					foreach ($this->channels as $channel) {
						$channel->write($writable);
					}
				}
			}
		}

		/* Getters, setters and removers */
		final public function add_source($id, Source $source) {
			$this->sources[$id] = $source;
		}

		final public function add_channel($id, Channel $channel) {
			$this->channels[$id] = $channel;
		}

		final public function remove_source($id) {
			if (isset($this->sources[$id])) unset($this->sources[$id]);
		}

		final public function remove_channel($id) {
			if (isset($this->channels[$id])) unset($this->channels[$id]);
		}
	}
?>
