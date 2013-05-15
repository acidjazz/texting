<?

class img_ctl {

  // write filenames to disk that aren't found and then redirect
  public function pictures($filename) {

    list($data, $type) = explode('.', $filename);
    list($id, $width, $height) = explode('-', $data);

    $grid = contact::grid();
    $file = $grid->get(new MongoId($id));

    $image = imagecreatetruecolor($width, $height);
    $orig = imagecreatefromstring($file->getBytes());
    imagecopyresampled($image, $orig, 0, 0, 0, 0, $width, $height, imagesx($orig), imagesy($orig));

    imagejpeg($image, G_PATH.'img/pictures/'.$filename);
    header('Location: '.G_URL.'img/pictures/'.$filename);

  }

}
