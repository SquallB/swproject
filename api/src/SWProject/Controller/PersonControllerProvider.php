<?php

namespace SWProject\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use SWProject\Model\Person;
use SWProject\Model\PersonDAO;

class PersonControllerProvider implements ControllerProviderInterface {
	public function connect(Application $app)
    {
    	// creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        /**
         * @api {get} /person/ Requests all the flms
         * @apiName GetPersons
         * @apiGroup Person
         *
         * @apiSuccess {Array} persons The persons
         */
        $controllers->get('/', function (Application $app)  {
            $result = array();
            $status = 200;
            $personDAO = new PersonDAO();
            $result = $personDAO->findAll();

            return $app->json($result, $status);
        });

        /**
         * @api {get} /person/{id} Requests the person identified by the id
         * @apiName GetPerson
         * @apiGroup person
         *
         * @apiSuccess {person} person The person
         */
        $controllers->get('/{id}', function (Application $app, $id)  {
            $result = array();
            $status = 200;
            $personDAO = new PersonDAO();
            $result = $personDAO->find($id);

            return $app->json($result, $status);
        });

        /**
         * @api {post} /person/ Creates a new person.
         * @apiName PostPerson
         * @apiGroup person
         *
         * @apiParam {person} person   The person to add.
         *
         * @apiError ErrorWhileSaving The person couldn't be saved.
         */
        $controllers->post('/', function(Request $request) use($app) {
            $result = array();
            $status = 200;
            $personDAO = new PersonDAO();

            $person = new Person(json_decode($request->get('person'), true));
            $personId = $personDAO->save($person);

            if($personId !== null) {
                $person->setId($personId);
                $result = $person;
            }
            else {
                $result['message'] = 'Error while saving the person.';
                $status = 400;
            }

            return $app->json($result, $status);
        });

        return $controllers;
    }
}

?>