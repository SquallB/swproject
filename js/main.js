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
				$scope.film = null;
				var c = $('#thumbcarousel');
				$scope.nbActorsActive = Math.ceil($('#thumbcarousel').width() / 200);

				$scope.changeFilm = function(id) {
					$http({
					    method: 'GET',
					    url: 'http://localhost/swproject/api/film/' + id
					}).
					success(function(data, status) {
			          if(status === 200) {
			          	$scope.film = data;

			          	$scope.itemsRange = [];

			          	for(var i = $scope.nbActorsActive; i < data.people.actor.length; i += $scope.nbActorsActive) {
			          		$scope.itemsRange.push(i);
			          	}
			          }
			        });
			    }
			}],
			controllerAs: 'filmCtrl'
		};
	});
})();