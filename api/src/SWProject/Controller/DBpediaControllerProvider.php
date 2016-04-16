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
                SELECT ?name ?birthDate ?abstract ?picture
                WHERE {
                <'.$uri.'> dbo:'.$role.' ?starring.
                ?starring rdfs:label ?name;
                dbo:birthDate ?birthDate;
                dbo:abstract ?abstract.
                OPTIONAL {
                    ?starring dbo:thumbnail ?picture.
                }
                FILTER ( lang(?abstract) = "en" )
                FILTER ( lang(?name) = "en" )
            }';
        $actors = self::sparqlRequest($query);

        $array = array();
        foreach($actors as $actor) {
            //remove brackets in name
            $name = preg_replace('/\ *\([^)]+\)\ */', '', $actor['name']['value']);
            $lastSpace = strrpos($name, ' ');
            $firstName = substr($name, 0, $lastSpace);
            $lastName = substr($name, $lastSpace + 1);
            $picture = '';
            if(isset($film['picture'])) {
                $picture = $film['picture']['value'];
            }
            $person = new Person(array('first_name' => $firstName, 'last_name' => $lastName, 'birthdate' => $actor['birthDate']['value'], 'picture' => $picture));
            $array[] = $person;
        }

        return $array;
    }

    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        /**
         * @api {get} /dbpedia/ Get data from dbpedia
         * @apiName GetDBpediaData
         * @apiGroup DBpedia
         *
         * @apiSuccess {Array} films The films
         */
        $controllers->get('/', function (Application $app)  {
            $result = array();
            $status = 200;

            $query = '  PREFIX : <http://dbpedia.org/resource/>
                PREFIX dbpedia2: <http://dbpedia.org/property/>
                PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                PREFIX dbo: <http://dbpedia.org/ontology/>
                SELECT ?titre ?uri ?summary ?runtime ?picture 
                WHERE {
                    :Star_Wars dbpedia2:films ?uri.
                    ?uri rdfs:label ?titre;
                    dbo:abstract ?summary.                
                    OPTIONAL {
                        ?uri dbo:thumbnail ?picture;
                        dbo:runtime ?runtime.
                    }
                    FILTER ( lang(?titre) = "en" )
                    FILTER ( lang(?summary) = "en" )
                }';
            $films = self::sparqlRequest($query);

            foreach ($films as $film) {
                $uri = $film['uri']['value'];
                $picture = '';
                if (isset($film['picture'])) {
                    $picture = $film['picture']['value'];
                }
                $runtime = 0;
                if (isset($film['runtime'])) {
                    $runtime = $film['runtime']['value'];
                }

                $filmObject = new Film(array('name' => $film['titre']['value'], 'summary' => $film['summary']['value'], 'release_date' => '1970-01-01', 'running_time' => intval($runtime), 'image' => $picture));
                $people = array();
                $people['actor'] = self::getPersons($uri, 'starring');
                $people['writer'] = self::getPersons($uri, 'writer');
                $people['director'] = self::getPersons($uri, 'director');
                $people['musicComposer'] = self::getPersons($uri, 'musicComposer');
                $filmObject->setPeople($people);

                $result[] = $filmObject;
            }

            if(count($result) === 0) {
                $status = 400;
            }

            return $app->json($result, $status);
        });

        return $controllers;
    }
}

?>