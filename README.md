## Intuji Google management sytem
This project is about event mangement system where use will connect to google calender anytime and mangement their events like create, view and delete.

## Technologies
- PHP
- Composer
- Html
- Css
- Javascript

## Environment Required
- Xampp/Apache
- PHP ^8
- (Or Docker)

## To Run this project
- With Xampp or Other local environment
    - php -S localhost:8000 -t ./public
    - [goto](http://localhost:8000)
- With Docker
    - cp .env.example .env
    - docker-compose build
    - docker-compose up
    - [goto](http://localhost:8088)

## Enviroment Setup For Oauth & Google Calendar Integration
 - Modify or update .env file variables after getting client_id, client_secret and api_key from google console dashboard
    - API_KEY
    - OAUTH_CLIENT_ID
    - OAUTH_CLIENT_SECRET