<?php

namespace SWProject\Model;
use \JsonSerializable;

class Film implements JsonSerializable {
	private $id;
	private $name;
	private $releaseDate;
	private $runningTime;
	private $image;
	private $people;

	public function __construct($data = array()) {
		if(is_array($data)) {
			if(isset($data['id'])) {
				$this->setId($data['id']);
			}

			if(isset($data['name'])) {
				$this->setName($data['name']);
			}

			if(isset($data['release_date'])) {
				$this->setReleaseDate($data['release_date']);
			}

			if(isset($data['image'])) {
				$this->setImage($data['image']);
			}

			if(isset($data['running_time'])) {
				$this->setRunningTime($data['running_time']);
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

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		if(is_string($name)) {
			$this->name = $name;
		}
	}

	public function getReleaseDate() {
		return $this->releaseDate;
	}

	public function setReleaseDate($releaseDate) {
		$this->releaseDate = $releaseDate;
	}

	public function getRunningTime() {
		return $this->runningTime;
	}

	public function setRunningTime($runningTime) {
		if(is_numeric($runningTime)) {
			$this->runningTime = $runningTime;
		}
	}

	public function getImage() {
		return $this->image;
	}

	public function setImage($image) {
		if(is_string($image)) {
			$this->image = $image;
		}
	}

	public function getPeople() {
		return $this->people;
	}

	public function setPeople($people) {
		if(is_array($people)) {
			$this->people = $people;
		}
	}

	public function jsonSerialize() {
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'release_date' => $this->getReleaseDate(),
			'running_time' => $this->getRunningTime(),
			'image' => $this->getImage(),
			'people' => $this->getPeople()
		];
	}
}