<?php
include_once ("eyeem-API-php-wrapper/lib/Eyeem.php");

class WP_EyeEm
{
	protected $eyeem;
	protected $user;
	protected $photos;
	protected $maxWidth;
		
	public function __construct($username, $maxsize)
	{
		$this->maxWidth = $maxsize;
		$this->initializeEyeEm();
		
		try {
			$this->user = $this->eyeem->getUser($username);
		} 
		catch (Exception $e) {
			throw new Exception($e);
		}
		
		try {
			$this->photos = $this->user->getPhotos();
		}
		catch (Exception $e)
		{
			throw new Exception($e);
		}
		
	}
	
	public function getUser()
	{
		return $this->user;
	}
	
	public function getPhotos()
	{
		return $this->photos;	
	}
	
	private function initializeEyeEm()
	{
		$this->eyeem = new Eyeem();
		$this->eyeem->setClientId('ujbmbBzApRTAodnjKwZ35s2DBWkJ1F4v');
		$this->eyeem->setClientSecret('qvpBytHExndxIIC02NqefEbP9LCXgvNU');
		$this->eyeem->autoload();
		$this->eyeem->setAccessToken('ujbmbBzApRTAodnjKwZ35s2DBWkJ1F4v');
		
		// Cache (optional)
		if (extension_loaded('memcache')) {
			$memcache = new Memcache;
			$memcache->addServer('localhost', 11211);
			Eyeem_Cache::setMemcache($memcache);
		} elseif (is_writable(__DIR__ . '/tmp')) {
			Eyeem_Cache::setTmpDir(__DIR__ . '/tmp');
		}
	}
	
	public function getMaxWidthPhotoUrl($photo)
	{
		return ($this->maxWidth != null) ? str_replace("640/480", "550/480", $photo->photoUrl) : $photo->photoUrl;
		
	}
}
?>
