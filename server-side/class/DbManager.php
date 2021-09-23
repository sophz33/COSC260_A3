<?php

class DbManager
{
  const DATA_FILE = '../id.json';


  public function getAllPersons() {
    $data = $this->getData();
    $persons = [];

    foreach ($data['polls'] as $poll) {
      $persons[] = new Database($persons['name'], $persons['age'], $persons['email'], $persons['phone']);
    }

    return $persons;
  }



  public function getID($id) {
    $data = $this->getData();

    foreach ($data['polls'] as $poll) {
      if ($poll["id"] === $id) {
        return new Database($persons['name'], $persons['age'], $persons['email'], $persons['phone']);
      }
    }

    return FALSE;
  }



  public function saveID($update) {
    $persons = $this->getAllPersons();

    for ($i = 0; $i < sizeof($polls); $i++) {
      if ($persons[$i]->getID() === $update->getID()) {
        $persons[$i] = $update;

        return file_put_contents(self::DATA_FILE, json_encode(['polls' => $polls]));
      }
    }

    return FALSE;
  }


  private function getData() {
    $json_data = file_get_contents(self::DATA_FILE);

    if ($json_data !== FALSE) {
      return json_decode($json_data, TRUE);
    }
    else {
      return FALSE;
    }
  }
}

// update a txt "database": check file exists, append otherwise create first
// public function saveID($results) {
//     if (file_exists("jsonDb.txt")) {
//         $txt = "jsonDb.txt";
//         $update = file_get_contents($txt);
//         return file_put_contents(self::DATA_FILE, json_encode(['polls' => $polls]));
//     } else {
//         $createFile = fopen("jsonDb.txt", "w");
//     }

// }