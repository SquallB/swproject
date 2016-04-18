<?php

namespace SWProject\Model;

class RoleDAO extends DAO {
	public function __construct(\PDO $connection = null) {
		parent::__construct($connection);
	}

	public function find($id) {
		$result = null;

		if(is_numeric($id) && $id > 0) {
			$parameters = array(':id' => $id);

			$stmt = $this->getConnection()->prepare('
				SELECT * FROM role WHERE id = :id
			');
			$stmt->execute($parameters);

			if($stmt->rowCount() > 0) {
				$result = new Film($stmt->fetch());
			}
		}

		return $result;
	}

	public function findAll() {
		$result = array();

		$stmt = $this->getConnection()->prepare('
			SELECT * FROM role ORDER BY name
		');
		$stmt->execute();

		foreach($stmt->fetchAll() as $row) {
			$result[] = new Film($row);
		}

		return $result;
	}

	public function save($data) {
		$id = null;

		if($data !== null && $data instanceof Role) {
			if($data->getId() !== null) {
				$id = $this->update($data);
			}
			else {
				$parameters = array(':name' => $data->getName());

				$stmt = $this->getConnection()->prepare('
					SELECT id FROM role WHERE name = :name
				');
				$stmt->execute($parameters);

				if($stmt->rowCount() > 0) {
					$data->setId($stmt->fetch()['id']);
					$id = $this->update($data);
				}
				else {
					$parameters = array(':name' => $data->getName());

					$stmt = $this->getConnection()->prepare('
						INSERT INTO role (name) VALUES (:name)
					');
					$stmt->execute($parameters);

					$id = $this->getConnection()->lastInsertId();
				}
			}
		}

		return $id;
	}

	public function update($data) {
		$id = null;

		if($data !== null && $data instanceof Role) {
			$parameters = array(':id' => $data->getId(), ':name' => $data->getName());

			$stmt = $this->getConnection()->prepare('
				UPDATE role SET name = :name WHERE id = :id
			');
			$stmt->execute($parameters);

			$id = $data->getId();
		}

		return $id;
	}

	public function delete($data) {
		$result = false;

		if($data !== null && $data instanceof Role) {
			$parameters = array(':id' => $data->getId());

			$stmt = $this->getConnection()->prepare('
				DELETE FROM role WHERE id = :id
			');
			$result = $stmt->execute($parameters);
		}

		return $result;
	}
}

?>