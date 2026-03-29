<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home
$routes->get('/', 'Movie::index');

// Movies
$routes->get('movie/search', 'Movie::search');
$routes->get('movie/nearby', 'Movie::nearby');
$routes->get('movie/detail/(:num)', 'Movie::detail/$1');
$routes->post('movie/addReview', 'Movie::addReview');
$routes->post('movie/deleteReview/(:num)', 'Movie::deleteReview/$1');
$routes->post('movie/toggleWatchlist', 'Movie::toggleWatchlist');
$routes->get('movie/watchlist', 'Movie::watchlist');

// User auth
$routes->get('user/register', 'User::register');
$routes->post('user/register', 'User::register');
$routes->get('user/login', 'User::login');
$routes->post('user/login', 'User::login');
$routes->get('user/logout', 'User::logout');
