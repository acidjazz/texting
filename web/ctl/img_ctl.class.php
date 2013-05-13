<?

class img_ctl {

  // write filenames to disk that aren't found and then redirect
  public function pictures($filename) {

    list($id, $type) = explode('.', $filename);

    $grid = contact::grid();
    $file = $grid->get(new MongoId($id));
    $file->write(G_PATH.'img/pictures/'.$filename);
    header('Location: '.G_URL.'img/pictures/'.$filename);

  }

}
