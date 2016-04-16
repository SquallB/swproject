<?php

$loader = require_once __DIR__.'/vendor/autoload.php';
$loader->add('SWProject', __DIR__.'/src');

use SWProject\Model\Person;
use SWProject\Model\Film;
use SWProject\Model\FilmDAO;

$filmDAO = new FilmDAO();

function sparqlRequest($query) {
    $url = 'http://dbpedia.org/sparql';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('format' => 'json', 'query' => $query));
    $result = json_decode(curl_exec ($ch), true)['results']['bindings'];
    curl_close($ch);

    return $result;
}

function getPersons($uri, $role) {
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
    $actors = sparqlRequest($query);

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

$query = '  PREFIX : <http://dbpedia.org/resource/>
			PREFIX dbpedia2: <http://dbpedia.org/property/>
			PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
			PREFIX dbo: <http://dbpedia.org/ontology/>
			SELECT ?titre ?uri ?summary ?runtime ?picture 
			WHERE {
				:Star_Wars dbpedia2:films ?uri.
				?uri rdfs:label ?titre;
				dbo:abstract ?summary;
                dbo:runtime ?runtime.
                OPTIONAL {
                    ?uri dbo:thumbnail ?picture.
                }
				FILTER ( lang(?titre) = "en" )
				FILTER ( lang(?summary) = "en" )
			}';
$films = sparqlRequest($query);

foreach ($films as $film) {
    $uri = $film['uri']['value'];
    $picture = '';
    if(isset($film['picture'])) {
        $picture = $film['picture']['value'];
    }

    $filmObject = new Film(array('name' => $film['titre']['value'], 'summary' => $film['summary']['value'], 'release_date' => '1970-01-01', 'running_time' => intval($film['runtime']['value']), 'image' => $picture));
    $people = array();
    $people['actor'] = getPersons($uri, 'starring');
    $people['writer'] = getPersons($uri, 'writer');
    $people['director'] = getPersons($uri, 'director');
    $people['musicComposer'] = getPersons($uri, 'musicComposer');
    $filmObject->setPeople($people);

    echo $filmDAO->save($filmObject);
}

?>