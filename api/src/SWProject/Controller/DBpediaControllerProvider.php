<?php

namespace SWProject\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use SWProject\Model\Film;
use SWProject\Model\Person;

class DBpediaControllerProvider implements ControllerProviderInterface {
    static function sparqlRequest($query) {
        $url = 'http://dbpedia.org/sparql';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('format' => 'json', 'query' => $query));
        $result = json_decode(curl_exec ($ch), true)['results']['bindings'];
        curl_close($ch);

        return $result;
    }

    static function getPersons($uri, $role) {
        $query = '  PREFIX dbo: <http://dbpedia.org/ontology/>
                    PREFIX dbp: <http://dbpedia.org/property/name/>
                    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                    SELECT ?name ?birthDate ?abstract
                    WHERE {
                    <http://dbpedia.org/resource/'.$uri.'> dbo:'.$role.' ?starring.
                    ?starring rdfs:label ?name;
                    dbo:birthDate ?birthDate;
                    dbo:abstract ?abstract.
                    FILTER ( lang(?abstract) = "en" )
                    FILTER ( lang(?name) = "en" )
            }';
        $actors = self::sparqlRequest($query);

        $array = array();
        if(is_array($actors)) {
            foreach ($actors as $actor) {
                //remove brackets in name
                $name = preg_replace('/\ *\([^)]+\)\ */', '', $actor['name']['value']);
                $lastSpace = strrpos($name, ' ');
                $firstName = substr($name, 0, $lastSpace);
                $lastName = substr($name, $lastSpace + 1);
                $picture = '';

                $person = new Person(array('first_name' => $firstName, 'last_name' => $lastName, 'birthdate' => $actor['birthDate']['value'], 'picture' => $picture));
                $array[] = $person;
            }
        }

        return $array;
    }

    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        /**
         * @api {get} /dbpedia/film Get films from dbpedia
         * @apiName GetDBpediaFilms
         * @apiGroup DBpedia
         *
         * @apiSuccess {Array} films The films
         */
        $controllers->get('/film', function (Application $app)  {
            $result = array();
            $status = 200;

            $query = '  PREFIX : <http://dbpedia.org/resource/>
                        PREFIX dbpedia2: <http://dbpedia.org/property/>
                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                        PREFIX dbo: <http://dbpedia.org/ontology/>
                        PREFIX dct: <http://purl.org/dc/terms/>
                        SELECT ?titre ?uri ?summary ?runtime ?yearCategory
                        WHERE {
                            :Star_Wars dbpedia2:films ?uri.
                            ?uri rdfs:label ?titre;
                            dbo:abstract ?summary;
                            dct:subject ?category.
                            ?category rdfs:label ?yearCategory.

                            OPTIONAL {
                                ?uri dbo:runtime ?runtime.
                            }
                            
                            FILTER ( lang(?titre) = "en" ).
                            FILTER ( lang(?summary) = "en" ).
                            FILTER ( lang(?yearCategory) = "en" ).
                            FILTER regex ( ?yearCategory, "[0-9]{4,4}? films" ).
                        }';
            $films = self::sparqlRequest($query);

            if(is_array($films)) {
                foreach ($films as $film) {
                    $picture = '';
                    $runtime = 0;
                    $year = substr($film['yearCategory']['value'], 0, 4);

                    if (isset($film['runtime'])) {
                        $runtime = $film['runtime']['value'] / 60;
                    }

                    $filmObject = new Film(array('name' => $film['titre']['value'], 'summary' => $film['summary']['value'], 'year' => $year, 'running_time' => intval($runtime), 'image' => $picture));
                    $result[] = $filmObject;
                }
            }

            if(count($result) === 0) {
                $status = 400;
            }
            else {
                usort($result, array("SWProject\\Model\\Film", "compare"));
            }

            return $app->json($result, $status);
        });

        /**
         * @api {get} /dbpedia/film/people/filmURI Get more details about a film, including actors, writers...
         * @apiName GetDBpediaFilmsPeople
         * @apiGroup DBpedia
         *
         * @apiSuccess {Array} films The people who had something to do with the film
         */
        $controllers->get('/film/people/{filmURI}', function (Application $app, $filmURI) {
            $filmURI = str_replace(" ", "_", $filmURI);
            $people = array();

            $people['actor'] = self::getPersons($filmURI, 'starring');
            $people['writer'] = self::getPersons($filmURI, 'writer');
            $people['director'] = self::getPersons($filmURI, 'director');
            $people['composer'] = self::getPersons($filmURI, 'musicComposer');

            return $app->json($people, 200);
        });

        return $controllers;
    }
}

?>