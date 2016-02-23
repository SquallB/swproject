define({ "api": [
  {
    "type": "get",
    "url": "/film/{id}",
    "title": "Requests the film identified by the id",
    "name": "GetFilm",
    "group": "Film",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Film</p> ",
            "optional": false,
            "field": "film",
            "description": "<p>The film</p> "
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "./src/SWProject/Controller/FilmControllerProvider.php",
    "groupTitle": "Film"
  },
  {
    "type": "get",
    "url": "/film/",
    "title": "Requests all the flms",
    "name": "GetFilms",
    "group": "Film",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "films",
            "description": "<p>The films</p> "
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "./src/SWProject/Controller/FilmControllerProvider.php",
    "groupTitle": "Film"
  },
  {
    "type": "post",
    "url": "/film/",
    "title": "Creates a new Film.",
    "name": "PostFilm",
    "group": "Film",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Film</p> ",
            "optional": false,
            "field": "film",
            "description": "<p>The film to add.</p> "
          }
        ]
      }
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "ErrorWhileSaving",
            "description": "<p>The film couldn't be saved.</p> "
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "./src/SWProject/Controller/FilmControllerProvider.php",
    "groupTitle": "Film"
  },
  {
    "type": "get",
    "url": "/person/",
    "title": "Requests all the flms",
    "name": "GetPersons",
    "group": "Person",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>Array</p> ",
            "optional": false,
            "field": "persons",
            "description": "<p>The persons</p> "
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "./src/SWProject/Controller/PersonControllerProvider.php",
    "groupTitle": "Person"
  },
  {
    "type": "get",
    "url": "/person/{id}",
    "title": "Requests the person identified by the id",
    "name": "GetPerson",
    "group": "person",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "<p>person</p> ",
            "optional": false,
            "field": "person",
            "description": "<p>The person</p> "
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "./src/SWProject/Controller/PersonControllerProvider.php",
    "groupTitle": "person"
  },
  {
    "type": "post",
    "url": "/person/",
    "title": "Creates a new person.",
    "name": "PostPerson",
    "group": "person",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>person</p> ",
            "optional": false,
            "field": "person",
            "description": "<p>The person to add.</p> "
          }
        ]
      }
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "ErrorWhileSaving",
            "description": "<p>The person couldn't be saved.</p> "
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "./src/SWProject/Controller/PersonControllerProvider.php",
    "groupTitle": "person"
  }
] });