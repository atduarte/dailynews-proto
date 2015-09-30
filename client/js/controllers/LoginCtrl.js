'use strict';

var newsAppController = angular.module('angularControllers');

newsAppController.controller('LoginCtrl', ['$scope','$location','$http','config',
    function($scope,$location,$http,config){
        $scope.user = {};

        $scope.login = function(){
            $http({
                url: config.routes.api.login($scope.user.username),
                method: 'POST'
            }).success(function(){
                //auth.login($scope.user.username);
                $location.path('/user/'+$scope.user.username);
                console.log("success");
            }).error(function(){
                //$location.path('/user/'+$scope.user.username);
                auth.login($scope.user.username);

                console.log("success");
            });

		};			
	}
]);