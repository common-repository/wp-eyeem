<?php

class Eyeem_Photo extends Eyeem_Ressource
{

  public static $name = 'photo';

  public static $endpoint = '/photos/{id}';

  public static $properties = array(
    /* Basic */
    'id',
    'thumbUrl',
    'photoUrl',
    'width',
    'height',
    'updated',
    /* Detailed */
    'webUrl',
    'user',
    'caption',
    'totalLikes',
    'totalComments'
  );

  public static $collections = array(
    'likers' => 'user',
    'albums' => 'album',
    'comments' => 'comment'
  );

  public static $parameters = array(
    'detailed',
    'includeComments',
    'numComments',
    'includeLikers',
    'numLikers',
    'includeAlbums',
    'numAlbums'
  );

  protected $_queryParameters = array(
    'includeComments' => false,
    'includeLikers' => false,
    'includeAlbums' => false
  );

  public function getUser()
  {
    $user = parent::getUser();
    return $this->getRessourceObject('user', $user);
  }

  // Helper to get a Thumb Url

  public function getThumbUrl($width = 'h', $height = '100')
  {
    $thumbUrl = $this->thumbUrl;
    if ($height != '100') {
      $thumbUrl = str_replace('/thumb/h/100/', "/thumb/h/$height/", $thumbUrl);
    }
    if ($width != 'h') {
      $thumbUrl = str_replace('/thumb/h/', "/thumb/$width/", $thumbUrl);
    }
    return $thumbUrl;
  }

  // For Authenticated Users

  public function like()
  {
    $me = $this->getEyeem()->getAuthUser();
    $this->getLikers()->add($me);
    $me->getLikedPhotos()->flush();
    return $this;
  }

  public function unlike()
  {
    $me = $this->getEyeem()->getAuthUser();
    $this->getLikers()->remove($me);
    $me->getLikedAlbums()->flush();
    return $this;
  }

  public function postComment($params = array())
  {
    if (is_string($params)) {
      $params = array('message' => $params);
    }
    $response = $this->getComments()->post($params);
    return $this->getRessourceObject('comment', $response['comment']);
  }

  public function addAlbum($album)
  {
    $album = $this->getEyeem()->getAlbum($album);
    $this->getAlbums()->add($album);
    $album->getPhotos()->flush();
    return $this;
  }

  public function removeAlbum($album)
  {
    $album = $this->getEyeem()->getAlbum($album);
    $this->getAlbums()->remove($album);
    $album->getPhotos()->flush();
    return $this;
  }

  public function delete()
  {
    $this->getUser()->getPhotos()->flush();
    foreach ($this->getAlbums() as $album) {
      $album->getPhotos()->flush();
    }
    foreach ($this->getLikers() as $liker) {
      $liker->getLikedPhotos()->flush();
    }
    return parent::delete();
  }

}
