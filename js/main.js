(function() {
	Date.prototype.toJSON = function() {
		year = this.getFullYear();

		month = this.getMonth() + 1;
		if(month < 10) {
			month = "0" + month;
		}

		date = this.getDate();
		if(date < 10) {
			date = "0" + date;
		}

	    return year + "-" + month + "-" + date;
	};

	var app = angular.module('filmsManager', []);

	app.directive('filmForm', function() {
		return {
			restrict: 'E',
			templateUrl: 'film-form.html',
			controller: ['$http', function($http) {
				this.initFilm = function() {
					this.film = {};
					this.film.name = "";
					this.film.release_date = new Date();
					this.film.running_time = 0;
				}

				this.initFilm();

				this.submit = function() {
					$http({
					    method: 'POST',
					    url: 'http://localhost/swproject/api/film/',
					    data: "film=" + JSON.stringify(this.film),
					    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					});
					this.initFilm();
				}
			}],
			controllerAs: 'form'
		};
	});

	app.directive('film', function() {
		return {
			restrict: 'E',
			templateUrl: 'film.html',
			controller: ['$http', '$scope', function($http, $scope) {
				$url = 'http://dbpedia.org/sparql';
				$query =   'PREFIX : <http://dbpedia.org/resource/>\
							PREFIX dbpedia2: <http://dbpedia.org/property/>\
							PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
							PREFIX dbo: <http://dbpedia.org/ontology/>\
							SELECT ?titre, ?uri WHERE {\
								:Star_Wars dbpedia2:films ?uri.\
								?uri rdfs:label ?titre.\
								FILTER ( lang(?titre) = "en" )\
							}';
				$http({
					method: 'POST',
					url: $url,
					headers: {
						'Content-type' : 'application/x-www-form-urlencoded',
		            	'Accept' : 'application/sparql-results+json'
		            },
	                params: { 
	                   query : $query,
	                   format: "json"
           			}
				}).
				success(function(data, status) {
					console.log(data);

					$query =   'PREFIX : <http://dbpedia.org/resource/>\
							PREFIX dbpedia2: <http://dbpedia.org/property/>\
							PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
							PREFIX dbo: <http://dbpedia.org/ontology/>\
							SELECT ?name, ?actorURI WHERE {\
								?uri dbo:starring ?actorURI.\
								?actorURI rdfs:label ?name.\
								FILTER ( lang(?name) = "en" )\
							}';
				})
				.error(function(data, status) {
					console.log('error');
				});

				$scope.film = null;
				var c = $('#thumbcarousel');
				$scope.nbActorsActive = Math.ceil($('#thumbcarousel').width() / 200);
				$scope.filmId = -1;

				$http({
				    method: 'GET',
				    url: 'http://localhost/swproject/api/film/'
				}).
				success(function(data, status) {
		          if(status === 200) {
		          	$scope.films = data;
		          	$scope.filmId = 0;
		          	$scope.changeFilm(0);		          	
		          }
		         });

				$scope.changeFilm = function() {
					$scope.film = $scope.films[$scope.filmId];

					if($scope.film.people.actor !== undefined) {
						$scope.itemsRange = [];

			          	for(var i = $scope.nbActorsActive; i < $scope.film.people.actor.length; i += $scope.nbActorsActive) {
			          		$scope.itemsRange.push(i);
			          	}
		          	}
			    }

			    $scope.nextFilm = function() {
			    	if(($scope.filmId + 1) < $scope.films.length) {
			    		$scope.filmId++;
			    	}
			    	else {
			    		$scope.filmId = 0;
			    	}

			    	$scope.changeFilm();
			    }

			    $scope.previousFilm = function() {
			    	if(($scope.filmId - 1) > -1) {
			    		$scope.filmId--;
			    	}
			    	else {
			    		$scope.filmId = $scope.films.length -1;
			    	}

			    	$scope.changeFilm();
			    }
			}],
			controllerAs: 'filmCtrl'
		};
	});
})();