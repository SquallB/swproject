<?php

namespace SWProject\Model;

class PersonDAO extends DAO {
	public function __constrct(PDO $connection = null) {
		parent::__constrct($connection);
	}

	public function find($id) {
		$result = null;

		if(is_numeric($id) && $id > 0) {
			$parameters = array(':id' => $id);

			$stmt = $this->getConnection()->prepare('
				SELECT * FROM person WHERE id = :id
			');
			$stmt->execute($parameters);

			if($stmt->rowCount() > 0) {
				$result = new Person($stmt->fetch());
			}
		}

		return $result;
	}

	public function findAll() {
		$result = array();

		$stmt = $this->getConnection()->prepare('
			SELECT * FROM person ORDER BY last_name
		');
		$stmt->execute();

		foreach($stmt->fetchAll() as $row) {
			$result[] = new Person($row);
		}

		return $result;
	}

	public function save($data) {
		$id = null;

		if($data !== null && $data instanceof Person) {
			if($data->getId() !== null) {
				$id = $this->update($data);
			}
			else {
				$parameters = array(':first_name' => $data->getFirstName(), ':last_name' => $data->getLastName(), ':birthdate' => $data->getBirthdate(),
									':picture' => $data->getPicture());

				$stmt = $this->getConnection()->prepare('
					INSERT INTO person (first_name, last_name, birthdate, picture) VALUES (:first_name, :last_name, :birthdate, :picture)
				');
				$stmt->execute($parameters);

				$id = $this->getConnection()->lastInsertId();
			}
		}

		return $id;
	}

	public function update($data) {
		$id = null;

		if($data !== null && $data instanceof Person) {
			$parameters = array(':id' => $data->getId(), ':first_name' => $data->getFirsName(), ':last_name' => $data->getLastName(),
								':birthdate' => $data->getBirthdate(), ':picture' => $data->getPicture());

			$stmt = $this->getConnection()->prepare('
				UPDATE person SET first_name = :first_name, last_name = :last_name, birthdate = :birthdate, picture = :picture WHERE id = :id
			');
			$stmt->execute($parameters);

			$id = $data->getId();
		}

		return $id;
	}

	public function delete($data) {
		$result = false;

		if($data !== null && $data instanceof Person) {
			$parameters = array(':id' => $dat->getId());

			$stmt = $this->getConnection()->prepare('
				DELETE FROM person WHERE id = :id
			');
			$result = $stmt->execute($parameters);
		}

		return $result;
	}
}

?>