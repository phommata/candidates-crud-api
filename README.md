# PHP Interview
Thank you for your time. We would like to do a coding exercise with you to see how you might fit in with our team.

Please see your interviewer for all questions not answered in this document.

## Application Overview
Today we will be building a RESTful employee candidate API. We will call it the "Candidate API".
Some of the scaffolding is already created for you. Please take a moment to get acquainted with it.

This is built using Slim Framework. It is strictly for the purpose of this interview. We do not use Slim Framework.
Do not get overly concerned with the framework itself. Hopefully this README will give you much of the information you
need. If there are any other questions, please ask the interviewer.

It uses PHP 7.4 and MySQL 8. When you setup the project in your IDE, you will want to set it up for PHP 7.4. Use any and
all the features of PHP 7.4 and MySQL 8.

### Directory Structure
* `/db/install.sql` sets up a database
* `/public/index.php` the index file
* `/src/` where the majority of the application is located
  * `Api/` the API module. This is some basic scaffolding for a REST API.
    * `Controller/` this is all copied from the Slim Framework skeleton. It mainly sets up the front controller
    * `Model/Permissions.php` where you will find the permissions for the API. See "Authentication and Authorization" below for more
  * `Candidate/` the Candidate module. This is the basics for handling a candidate including the interfaces
    * `Model/Candidate` the candidate entity object
    * `Model/CandidateRepository` the candidate repository - connects the data source to the app
  * `CandidateApi/`
    * `Controller/GetCandidate` get candidate route
    * `Controller/ListCandidates` list candidate route
  * `dependencies.php` the DIC mapping
  * `routes.php` the routes
  * `settings.php` other settings (just the logging is in here)

## Setup

1. Setup a new project in your preferred IDE with these project files
2. Download and install [Docker](https://docs.docker.com/get-docker/) on your computer
3. Run `docker-compose up -d` from this directory
4. Run `cp-vendor-dir.sh` (you may have to `chmod +x cp-vendor-dir.sh` first)

## Accessing the API
I recommend you use Postman to access the API, but if you have another API tool you prefer, feel free to use it.

After you setup the application, it should be running on localhost port 80. If you have anything else running on localhost:80,
you will need to stop that before starting the application.

### Authentication and Authorization
If you supply a header "user-group" with the value of "authenticated", you will be authenticated and authorized to do 
all the actions. Any other value for this header, including not supplying it, will result in the user being a guest.
Guests have no permissions. You can see the permissions in the `Promenade\Interview\Api\Model\Permissions` class.

If you get a 403, it means you don't have the "user-group: authenticated" header.

### Example Requests

#### Get Candidate with ID of 1
This is a request to get the candidate with an ID of "1"

##### Request
```bash
curl --location --request GET 'http://localhost/candidate/1' \
--header 'Content-Type: application/json' \
--header "user-group: authenticated"
```

##### Response
```json
{
    "statusCode": 200,
    "data": {
        "id": "1",
        "first_name": "John",
        "last_name": "Jameson",
        "email": "john@jameson.com",
        "created_at": "2020-07-01T12:33:04"
    }
}
```

#### Get Candidate with ID of 99
This is a request to get the candidate with an ID of "1"

##### Request
```bash
curl --location --request GET 'http://localhost/candidate/99' \
--header 'Content-Type: application/json' \
--header "user-group: authenticated"
```

##### Response
```json
{
  "statusCode": 404,
  "data": {
    "error": "Candidate not found",
    "context": {
      "candidate_id": "99"
    }
  }
}
```

#### Get Candidate without an authorized user
This request does not supply the "user-group" header. 
Also supply anything other than "authenticated" for the value of this header will return the same response.

##### Request
```bash
curl --location --request GET 'http://localhost/candidate/1' \
--header 'Content-Type: application/json'
```

##### Response
```json
{
  "statusCode": 403
}
```

### Creating a new action
First, take a look at `GetCandidate.php`.

1. Create a class that extends `AbstractAction` and implement the abstract method from it.
2. Add a new route to `routes.php`. The methods of `$group` are the same as the common HTTP methods: post, get, put, patch, delete, etc.
   Place all of the routes inside of the `/candidate` group.
3. Test that it works

Use the model classes that are created. As you go through the exercises, you may need to implement new methods.
Take a look at the body of the method before you use it blindly. There may be mistakes and errors that may or may not be
placed there purposefully. Check your code.


## Docker
We use Docker for development at Promenade. If you are unfamiliar with what it is or how it works, see https://docs.docker.com

Make sure this directory is accessible via Docker. Go into the file-sharing area of Docker Desktop to set that up.

### Docker Compose
After you've installed Docker and Docker Compose, you will have the ability to run the services in `docker-compose.yml`.

There are two services: "app" and "db". The app service creates an image defined in `Dockerfile` in this directory. This
is the PHP application and where your code from this project will be found. The db is a MySQL image.

### Docker Commands
When you run `docker-compose up -d` you will start the application. After that you can access the images via the CLI.

#### Start/Stop Services
`docker-compose up -d` start as daemon
`docker-compose up` start as not-daemon
`docker-compose stop` stop the services
`docker stop $(docker ps -q)` stop all containers

#### List Containers
`docker ps` list the running containers
`docker ps -a` list all containers (includes stopped containers)

#### App
`docker-compose exec app bash` this will give you a bash shell into the running app container
`docker-compose logs app` view the logs for this container
`docker-compose logs -f app` tail the logs for this container

#### Database
`docker-compose exec db bash` this will give you a bash shell into the running db container
`docker-compose exec db mysql -uroot -pinterview interview` this will connect you directly to the database

#### View logs
`docker-compose logs <service>` view the logs for this container. `<service>` is either "app" or "db" 
`docker-compose logs -f <service>` tail the logs for this container. `<service>` is either "app" or "db"

If you are having a problem with it starting the containers, you can view all the logs on startup by not daemonizing 
the containers: `docker-compose up`.

## Testing and debugging
Xdebug is installed in the container. The serverName is "promenade.co".


PhpUnit is installed. You can execute it by running `composer test`.
Any files that end in `Test.php` will be executed as a test. You can see more in `src/CandidateApi/Tests/GetCandidateTest.php`.

