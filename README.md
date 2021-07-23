# El Blog
> A small blog built from the ground-up. Nothing special, just a simple CRUD application.

## Table of contents
* [General info](#general-info)
* [Screenshots](#screenshots)
* [Technologies](#technologies)
* [Setup](#setup)
* [Features](#features)
* [Status](#status)
* [Credits](#credits)

## General info
This project was to practice and demonstrate my knowledge of procedural PHP7+ and SQL, primarily.
After completing an online procedural PHP course, I wanted to build something from zero to test the knowledge I had learned.

## Screenshots
None yet.

## Technologies
* PHP version 7.4.11
* MySQL database (personal setup using XAMPP)
* jQuery
* Bootstrap

## Setup
Download code, import portfolioblog.sql database to your MySQL installation.
To access admin functions, you will need to create an admin user first - do this via SQL.
INSERT INTO users (user_username, user_password, user_firstname, user_lastname, user_role, user_email) VALUES ("USERNAME", "PASSWORD", "FNAME", "LNAME", "admin", "EMAIL")
From the admin section, you will have to edit your own user to hash the password so you can log in properly.
Until then, you will have to comment out the password encryption in login.php so you can log in with your unhashed password to access the admin section.
Make sure to restore the encrypt function after doing this!

## Features
List of features ready and TODOs for future development
* Ability to post, like posts, comment on posts, like comments
* 3 user types: admin (all access), writer (only allowed to write posts), subscriber (only allowed to like and comment, cannot access admin)

To-do list:
* Introduce WYSIWYG editor and allow DB to accept HTML
* Post categories should be displayed or actually do something

## Status
Project is: _in progress_ focusing on other projects at the moment.

## Credits
Theme, blog:
Start Bootstrap - Clean Blog v5.0.10 (https://startbootstrap.com/theme/clean-blog) licensed under MIT (https://github.com/StartBootstrap/startbootstrap-clean-blog/blob/master/LICENSE)
Theme, admin CMS: KLOROFIL - Free Bootstrap Dashboard Template, Version: 2.0, Author: The Develovers, https://www.themeineed.com/, license: Creative Common Attribution 4.0 https://creativecommons.org/licenses/by/4.0/
