class TwitterSearch(tb.Source):
	"""

	"""
	def __init__(self, twitter, q, count=10, actions=['default']):
		self.q = q
		self.count = count
		self.api = twitter.api
		self.actions = actions
		self.max_id = 0

	def read(self):
		response = self.api.get('search', {'q': self.q, 'rpp': self.count, 'since_id': self.max_id})
		self.max_id= response['max_id']
		results = response['results']

		for tweet in results:
			writable = tb.Writable(title=tweet['text'], author=tweet['from_user'])
			writable.tweet_id = tweet['id']
			writable.actions = self.actions
			yield writable
