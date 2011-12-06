<?php
	/* A Writable object - an object that can be written */
	class Writable {
		public function __construct($args) {
			$this->title = isset($args['title'])? $args['title'] : '';
			$this->excerpt = isset($args['excerpt'])? $args['excerpt'] : '';
			$this->content = isset($args['content'])? $args['content'] : '';
			$this->permalink = isset($args['permalink'])? $args['permalink'] : '';
			$this->author = isset($args['author'])? $args['author'] : '';
			$this->tags = isset($args['tags'])? $args['tags'] : array();

			$this->actions = isset($args['actions'])? $args['actions'] : array();
			$this->timestamp = time();
		}
	}
?>
