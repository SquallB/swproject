(function() {
	Date.prototype.toJSON = function() {
		var year = this.getFullYear();

		var month = this.getMonth() + 1;
		if(month < 10) {
			month = "0" + month;
		}

		var date = this.getDate();
		if(date < 10) {
			date = "0" + date;
		}

		return year + "-" + month + "-" + date;
	};

	var app = angular.module('filmsManager', []);

	app.directive('film', function() {
		return {
			restrict: 'E',
			templateUrl: 'film.html',
			controller: ['$http', '$scope', function($http, $scope) {
				$scope.film = null;
				$scope.films = [];
				$scope.actor = null;
				$scope.nbActorsActive = Math.ceil($('#thumbcarousel').width() / 200);
				$scope.filmId = -1;

				$http({
					method: 'GET',
					url: 'http://localhost/swproject/api/dbpedia/film'
				}).then(function successCallback(response) {
					if (response.status === 200) {
						$scope.films = response.data;
						$scope.filmId = 0;
						$scope.changeFilm(0);

						for (var index in $scope.films) {
							(function () {
								var film = $scope.films[index];
								film.people = [];

								$http({
									method: 'GET',
									url: 'http://localhost/swproject/api/dbpedia/film/people/' + film.name
								}).then(function successCallback(response) {
									if (response.status === 200) {
										film.people = response.data;
										$scope.changeActors();

										$http({
											method: 'POST',
											url: 'http://localhost/swproject/api/film/',
											data: "film=" + encodeURIComponent(JSON.stringify(film)),
											headers: {'Content-Type': 'application/x-www-form-urlencoded'}
										});
									}
								}, function errorCallback(response) {});
							})();
						}
					}
				}, function errorCallback(response) {
					$http({
						method: 'GET',
						url: 'http://localhost/swproject/api/film/'
					}).then(function successCallback(response) {
						if (response.status === 200) {
							$scope.films = response.data;
							$scope.filmId = 0;
							$scope.changeFilm(0);
						}
					}, function errorCallback(response) {});
				});

				$scope.changeActors = function() {
					if($scope.film && $scope.film.people && $scope.film.people.actor) {
						$scope.itemsRange = [];

						for (var i = $scope.nbActorsActive; i < $scope.film.people.actor.length; i += $scope.nbActorsActive) {
							$scope.itemsRange.push(i);
						}
					}
				};

				$scope.changeFilm = function() {
					if($scope.films.length > 0) {
						$scope.film = $scope.films[$scope.filmId];

						$scope.changeActors();
					}
				};

				$scope.nextFilm = function() {
					if(($scope.filmId + 1) < $scope.films.length) {
						$scope.filmId++;
					}
					else {
						$scope.filmId = 0;
					}

					$scope.changeFilm();
				};

				$scope.previousFilm = function() {
					if(($scope.filmId - 1) > -1) {
						$scope.filmId--;
					}
					else {
						$scope.filmId = $scope.films.length -1;
					}

					$scope.changeFilm();
				};

				$scope.displayModal = function(actor) {
					$scope.actor = actor;
					$("#modal-actor").modal("show");
				};
			}],
			controllerAs: 'filmCtrl'
		};
	});
})();