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
                    PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                    SELECT ?name (SAMPLE(?bd) AS ?birthDate) (SAMPLE(?abs) AS ?abstract) (SAMPLE(?pic) AS ?picture)
                    WHERE {
                        <http://dbpedia.org/resource/'.$uri.'> dbo:'.$role.' ?person.
                        ?person rdfs:label ?name;
                        dbo:birthDate ?bd;
                        dbo:abstract ?abs.
                        OPTIONAL {
                            ?person foaf:depiction ?pic.
                        }
                        FILTER ( lang(?abs) = "en" )
                        FILTER ( lang(?name) = "en" )
                    }
                    GROUP BY ?name';
        $persons = self::sparqlRequest($query);

        $array = array();
        if(is_array($persons)) {
            foreach ($persons as $person) {
                //remove brackets in name
                $name = preg_replace('/\ *\([^)]+\)\ */', '', htmlentities($person['name']['value']));
                $lastSpace = strrpos($name, ' ');
                $firstName = substr($name, 0, $lastSpace);
                $lastName = substr($name, $lastSpace + 1);
                $picture = '';
                if(isset($person['picture'])) {
                    $picture = htmlentities($person['picture']['value']);
                }
                $birthdate = htmlentities($person['birthDate']['value']);
                //check si la date contient seulement l'année
                if(strlen($birthdate) === 4) {
                    $birthdate .= '-01-01';
                }
                $summary = htmlentities($summary = $person['abstract']['value']);

                $person = new Person(array('first_name' => $firstName, 'last_name' => $lastName, 'birthdate' => $birthdate, 'picture' => $picture, 'summary' => $summary));
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
                        SELECT DISTINCT ?titre ?uri ?summary ?runtime ?yearCategory
                        WHERE {
                            :Star_Wars dbpedia2:films ?uri.
                            ?uri rdfs:label ?titre;
                            dbo:abstract ?summary;
                            dct:subject ?category.
                            ?category rdfs:label ?yearCategory.
                            OPTIONAL {
                                ?uri dbo:runtime ?runtime.
                                ?uri foaf:depiction ?picture.
                            }
                            FILTER ( lang(?titre) = "en" ).
                            FILTER ( lang(?summary) = "en" ).
                            FILTER ( lang(?yearCategory) = "en" ).
                            FILTER regex ( ?yearCategory, "[0-9]{4,4}? films" ).
                        }';
            $films = self::sparqlRequest($query);

            if(is_array($films)) {
                foreach ($films as $film) {
                    $name = htmlentities($film['titre']['value']);
                    $picture = '';
                    if(isset($person['picture'])) {
                        $picture = htmlentities($film['picture']['value']);
                    }
                    $runtime = 0;
                    if (isset($film['runtime'])) {
                        $runtime = intval($film['runtime']['value'] / 60);
                    }
                    $year = htmlentities(substr($film['yearCategory']['value'], 0, 4));
                    $summary = htmlentities($film['summary']['value']);
                    $filmObject = new Film(array('name' => $name, 'summary' => $summary, 'year' => $year, 'running_time' => $runtime, 'image' => $picture));
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