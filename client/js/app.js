'use strict';

var newsApp = angular.module('newsApp', [
    'ngRoute',
    'newsAppServices',
    'newsAppDirectives',
    'newsAppConfig',
    'angularControllers'
]);


newsApp.config(function ($httpProvider) {
    $httpProvider.defaults.transformRequest = function(data){
        if (data === undefined) {
            return data;
        }
        return $.param(data);
    };
    $httpProvider.defaults.headers.post = {
        'Content-Type': 'application/x-www-form-urlencoded'
    };

});

newsApp.config(function($sceProvider) {
    // Completely disable SCE.  For demonstration purposes only!
    // Do not use in new projects.
    $sceProvider.enabled(false);
});

newsApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/', {
                redirectTo: '/login'
            }).when('/user/:username',{
                templateUrl : 'partials/home.html',
                controller  : 'HomeCtrl'
            }).when('/user/:username/news',{
                templateUrl : 'partials/news.html',
                controller  : 'NewsCtrl'
            }).when('/login',{
                templateUrl : 'partials/login.html',
                controller  : 'LoginCtrl'
            }).when('/error',{
                templateUrl : 'partials/error.html',
                controller  : 'ErrorCtrl'
            }).otherwise({
                redirectTo  : '/error'
            });
    }]);

