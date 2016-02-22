<?php

namespace SWProject\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use SWProject\Model\Film;
use SWProject\Model\FilmDAO;

class FilmControllerProvider implements ControllerProviderInterface {
	public function connect(Application $app)
    {
    	// creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        /**
         * @api {get} /film/ Requests all the flms
         * @apiName GetFilms
         * @apiGroup Film
         *
         * @apiSuccess {Array} films The films
         */
        $controllers->get('/', function (Application $app)  {
            $result = array();
            $status = 200;
            $filmDAO = new FilmDAO();
            $result = $filmDAO->findAll();

            return $app->json($result, $status);
        });

        /**
         * @api {get} /film/{id} Requests the film identified by the id
         * @apiName GetFilm
         * @apiGroup Film
         *
         * @apiSuccess {Film} film The film
         */
        $controllers->get('/{id}', function (Application $app, $id)  {
            $result = array();
            $status = 200;
            $filmDAO = new FilmDAO();
            $result = $filmDAO->find($id);

            return $app->json($result, $status);
        });

        /**
         * @api {post} /film/ Creates a new Film.
         * @apiName PostFilm
         * @apiGroup Film
         *
         * @apiParam {Film} film   The film to add.
         *
         * @apiError ErrorWhileSaving The film couldn't be saved.
         */
        $controllers->post('/', function(Request $request) use($app) {
            $result = array();
            $status = 200;
            $filmDAO = new FilmDAO();

            $film = new Film(json_decode($request->get('film'), true));
            $filmId = $filmDAO->save($film);

            if($filmId !== null) {
                $film->setId($filmId);
                $result = $film;
            }
            else {
                $result['message'] = 'Error while saving the film.';
                $status = 400;
            }

            return $app->json($result, $status);
        });

        return $controllers;
    }
}

?>