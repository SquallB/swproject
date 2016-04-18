<?php

namespace SWProject\Model;
use \JsonSerializable;

class Person implements JsonSerializable {
	private $id;
	private $firstName;
	private $lastName;
	private $birthdate;
	private $picture;
	private $character;
	private $summary;

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

			if(isset($data['picture'])) {
				$this->setPicture($data['picture']);
			}

			if(isset($data['character'])) {
				$this->setCharacter($data['character']);
			}

			$summary = '';
			if(isset($data['summary'])) {
				$summary = $data['summary'];
			}
			$this->setSummary($summary);
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

	public function getPicture() {
		return $this->picture;
	}

	public function setPicture($picture) {
		$this->picture = $picture;
	}

	public function getCharacter() {
		return $this->character;
	}

	public function setCharacter($character) {
		$this->character = $character;
	}

	public function getSummary() {
		return $this->summary;
	}

	public function setSummary($summary) {
		$this->summary = $summary;
	}

	public function jsonSerialize() {
		return [
			'id' => $this->getId(),
			'first_name' => $this->getFirstName(),
			'last_name' => $this->getLastName(),
			'birthdate' => $this->getBirthdate(),
			'picture' => $this->getPicture(),
			'character' => $this->getCharacter(),
			'summary' => $this->getSummary()
		];
	}
}