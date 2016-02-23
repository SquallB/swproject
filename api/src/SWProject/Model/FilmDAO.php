<?php

namespace SWProject\Model;

class FilmDAO extends DAO {
	public function __constrct(PDO $connection = null) {
		parent::__constrct($connection);
	}

	private function findPeople($id) {
		$result = array();
		$roles = array();
		$roleDAO = new RoleDAO($this->getConnection());
		$personDAO = new PersonDAO($this->getConnection());
		$parameters = array(':id' => $id);

		$stmt = $this->getConnection()->prepare('
			SELECT * FROM has_role WHERE id_film = :id
		');
		$stmt->execute($parameters);

		foreach($stmt->fetchAll() as $row) {
			$idRole = $row['id_role'];
			if(!isset($roles[$idRole])) {
				$roles[$idRole] = $roleDAO->find($idRole)->getName();
			}

			$result[$roles[$idRole]][] = $personDAO->find($row['id_person']);
		}

		return $result;
	}

	private function savePeople($film) {
		$people = $film->getPeople();
		$personDAO = new PersonDAO($this->getConnection());
		$peopleIds = array();

		$stmt1 = $this->getConnection()->prepare('
			SELECT * FROM has_role WHERE id_film = :id_film AND id_person = :id_person
		');

		$stmt2 = $this->getConnection()->prepare('
			INSERT INTO has_role (id_film, id_user) VALUES (:id_film, :id_person)
		');
		$parameters = array(':id_film' => $film->getId(), ':id_person' => 0);

		foreach($people as $person) {
			$peopleIds[] = $person->getId();
			$parameters[':id_person'] = $person->getId();

			$stmt1->execute($parameters);
			if($stmt1->rowCount() === 0) {
				$stmt2->execute($parameters);
			}			
		}

		$stmt = $this->getConnection()->prepare('
			SELECT id_person FROM has_role WHERE id_film = :id_film
		');

		$parameters2 = array(':id_film' => $film->getId());
		$stmt->execute($parameters2);

		$stmt2 = $this->getConnection()->prepare('
			DELETE FROM has_role WHERE id_film = :id_film AND id_person = :id_person
		');

		foreach($stmt->fetchAll() as $row) {
			if(!in_array($row['id_person'], $peopleIds)) {
				$parameters['id_person'] = $row['id_person'];
				$stmt2->execute($parameters);
			}
		}
	}

	public function find($id) {
		$result = null;

		if(is_numeric($id) && $id > 0) {
			$parameters = array(':id' => $id);

			$stmt = $this->getConnection()->prepare('
				SELECT * FROM film WHERE id = :id
			');
			$stmt->execute($parameters);

			if($stmt->rowCount() > 0) {
				$result = new Film($stmt->fetch());
				$result->setPeople($this->findPeople($id));
			}
		}

		return $result;
	}

	public function findAll() {
		$result = array();

		$stmt = $this->getConnection()->prepare('
			SELECT * FROM film ORDER BY name
		');
		$stmt->execute();

		foreach($stmt->fetchAll() as $row) {
			$film = new Film($row);
			$film->setPeople($this->findPeople($row['id']));
			$result[] = $film;
		}

		return $result;
	}

	public function save($data) {
		$id = null;

		if($data !== null && $data instanceof Film) {
			if($data->getId() !== null) {
				$id = $this->update($data);
			}
			else {
				$parameters = array(':name' => $data->getName(), ':release_date' => $data->getReleaseDate(), ':running_time' => $data->getRunningTime());

				$stmt = $this->getConnection()->prepare('
					INSERT INTO film (name, release_date, running_time) VALUES (:name, :release_date, :running_time)
				');
				$stmt->execute($parameters);

				$id = $this->getConnection()->lastInsertId();
			}
		}

		return $id;
	}

	public function update($data) {
		$id = null;

		if($data !== null && $data instanceof Film) {
			$parameters = array(':id' => $data->getId(), ':name' => $data->getName(), ':release_date' => $data->getReleaseDate(),
								':running_time' => $data->getRunningTime());

			$stmt = $this->getConnection()->prepare('
				UPDATE film SET name = :name, release_date = :release_date, running_time = :running_time WHERE id = :id
			');
			$stmt->execute($parameters);

			$id = $data->getId();
		}

		return $id;
	}

	public function delete($data) {
		$result = false;

		if($data !== null && $data instanceof Film) {
			$parameters = array(':id' => $dat->getId());

			$stmt = $this->getConnection()->prepare('
				DELETE FROM film WHERE id = :id
			');
			$result = $stmt->execute($parameters);
		}

		return $result;
	}
}

?>