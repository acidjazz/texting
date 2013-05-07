<?

class resizer {

  private $img;
  private $aspect;
  private $filters = array(
    'none' => false,
    'grayscale' => IMG_FILTER_GRAYSCALE,
    'edges' => IMG_FILTER_EDGEDETECT,
    'emboss' => IMG_FILTER_EMBOSS,
    'removal' => IMG_FILTER_MEAN_REMOVAL,
    'pixelate' => IMG_FILTER_PIXELATE
  );
  private $filter = IMG_FILTER_GRAYSCALE;

  public function __construct($url, $aspect=false, $filter='grayscale') {
    $this->img = $this->create($url);
    $this->aspect = $aspect;
    $this->filter = $this->filters[$filter];
  }

  public static function extension($header) {

    switch ($header) {

      case 'image/jpeg':
      case 'image/jpg':
        return '.jpg';
        break;

      case 'image/png':
        return '.png';
        break;

      case 'image/gif':
        return '.gif';
        break;

    }

  }

  public static function create($url) {

    list($dirname, $basename, $extension, $filename) = array_values(pathinfo($url));

    switch ($extension) {

      case 'jpg' :
      case 'jpeg' :
        return imagecreatefromjpeg($url);
        break;

      case 'gif' :
        return imagecreatefromgif($url);

      case 'png' :
        return imagecreatefrompng($url);

    }

  }

  public function resize($coords=false) {

    if ($coords != false && is_array($coords)) {

      list($x,$y,$x2,$y2) = array_values($coords);

      $nimg = imagecreatetruecolor($x2-$x,$y2-$y);
      imagecopyresampled($nimg, $this->img, 0, 0, $x, $y, $x2-$x, $y2-$y, $x2-$x, $y2-$y); 

    } else {

      $owidth = imagesx($this->img);
      $oheight = imagesy($this->img);
      $oaspect = ($owidth / $oheight);

      $x = $y = 0;


      if ($oaspect > $this->aspect) {

        $cwidth = $cheight = min($owidth, $oheight);
        $cwidth = round($cheight * $this->aspect);
        $x = ( $owidth - $cwidth ) /2;

        $nimg = imagecreatetruecolor($cwidth, $cheight);

        imagecopyresampled($nimg, $this->img, 0, 0, $x, $y, $owidth, $cheight, $owidth, $oheight);

      } else {

        $cwidth = $cheight = min($owidth, $oheight);

        $cheight = round($cwidth / $this->aspect);
        $y = ( $oheight - $cheight ) /2;

        $nimg = imagecreatetruecolor($cwidth, $cheight);
        imagecopyresampled($nimg, $this->img, 0, 0, $x, $y, $cwidth, $oheight, $owidth, $oheight);

      }

    }


    // black and white filter
    if ($this->filter != false) {
      imagefilter($nimg, $this->filter);
    }

    return $nimg;

  }

}
