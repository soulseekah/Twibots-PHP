<?php
	abstract class Channel {
		private $filters;

		public function filter(Writable $writable) {
			foreach ($this->filters as $filter)
				$writable = $filter->filter($writable);
			return $writable;
		}

		abstract function _default(Writable $writable);

		public function write(Writable $writable) {
			$writable = $this->filter($writable);
			if (get_class($writable) != 'Writable') {
				echo 'Was unable to process filter request';
				return false;
			}

			foreach ($writable->actions as $action) {
				if (method_exists($this, $action)) $this->$action($writable);
				elseif ($action == 'default') $this->_default($writable);
			}

			return $writable;
		}

		final public function remove_filter($id) {
			if (isset($this->filters[$id])) unset($this->filters[$id]);
		}

		final public function add_filter($id, Filter $filter) {
			$this->filters[$id] = $filter;
		}
	}
?>
