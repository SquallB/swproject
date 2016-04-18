<?php

namespace SWProject\Model;
use \JsonSerializable;

class Film implements JsonSerializable {
	private $id;
	private $name;
	private $year;
	private $runningTime;
	private $image;
	private $people;
	private $summary;

	public function __construct($data = array()) {
		if(is_array($data)) {
			if(isset($data['id'])) {
				$this->setId($data['id']);
			}

			if(isset($data['name'])) {
				$this->setName($data['name']);
			}

			if(isset($data['year'])) {
				$this->setYear($data['year']);
			}

			if(isset($data['image'])) {
				$this->setImage($data['image']);
			}

			if(isset($data['running_time'])) {
				$this->setRunningTime($data['running_time']);
			}

			$people = array();
			if(isset($data['people']) && is_array($data['people'])) {
				foreach($data['people'] as $role => $rolePeople) {
					$roleArray = array();
					if(is_array($rolePeople)) {
						foreach($rolePeople as $person) {
							$roleArray[] = new Person($person);
						}
					}
					$people[$role] = $roleArray;
				}
			}
			$this->setPeople($people);

			if(isset($data['summary'])) {
				$this->setSummary(html_entity_decode($data['summary']));
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

	public function getYear() {
		return $this->year;
	}

	public function setYear($year) {
		$this->year = $year;
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

	public function getSummary() {
		return $this->summary;
	}

	public function setSummary($summary) {
		$this->summary = $summary;
	}

	static function compare($a, $b) {
		if($a->getYear() === $b->getYear()) {
			return 0;
		}
		else if($a->getYear() > $b->getYear()) {
			return 1;
		}
		else if($a->getYear() < $b->getYear()) {
			return -1;
		}
	}

	public function jsonSerialize() {
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'year' => $this->getYear(),
			'running_time' => $this->getRunningTime(),
			'image' => $this->getImage(),
			'people' => $this->getPeople(),
			'summary' => $this->getSummary()
		];
	}
}