<?php

class Database implements JsonSerializable {

    public $name;
    public $age;
    public $email;
    public $phone;

    function _construct($name, $age, $email, $phone) {
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function getName() {
        return $this->name;
    }

    public function getAge() {
        return $this->age;
        echo("get age");
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPhone() {
        return $this->phone;
    }


    public function jsonSerialize() {
        return ['name' => $this->name,
                'age' => $this->age,
                'email' => $this->email,
                'phone' => $this->phone
            ];
    }

   

}


