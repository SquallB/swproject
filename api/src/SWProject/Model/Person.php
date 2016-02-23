<?php

namespace SWProject\Model;
use \JsonSerializable;

class Person implements JsonSerializable {
	private $id;
	private $firstName;
	private $lastName;
	private $birthdate;

	public function __construct($data = array()) {
		if(is_array($data)) {
			if(isset($data['id'])) {
				$this->setId($data['id']);
			}

			if(isset($data['first_name'])) {
				$this->setFirstName($data['first_name']);
			}

			if(isset($data['last_name'])) {
				$this->setLastName($data['last_name']);
			}

			if(isset($data['birthdate'])) {
				$this->setBirthdate($data['birthdate']);
			}
		}
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		if(is_numeric($id) && $id > 0) {
			$this->id = $id;
		}
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function setFirstName($firstName) {
		if(is_string($firstName)) {
			$this->firstName = $firstName;
		}
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function setLastName($lastName) {
		if(is_string($lastName)) {
			$this->lastName = $lastName;
		}
	}

	public function getBirthdate() {
		return $this->birthdate;
	}

	public function setBirthdate($birthdate) {
		$this->birthdate = $birthdate;
	}

	public function jsonSerialize() {
		return [
			'id' => $this->getId(),
			'first_name' => $this->getFirstName(),
			'last_name' => $this->getLastName(),
			'birthdate' => $this->getBirthdate()
		];
	}
}