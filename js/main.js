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
})();
