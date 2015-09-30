'use strict';

var newsAppController = angular.module('angularControllers');

newsAppController.controller('HomeCtrl', ['$scope','$routeParams','$http', 'config','flash',
    function($scope,$routeParams,$http,config,flash) {
        $scope.user = {};
        $scope.user.username = $routeParams.username;
        $scope.sources = {};
        $scope.newSource = '';

        var that = this;

        this.fetchSources = function(){
            console.log('fetch');
            $http({
                url: config.routes.api.source($routeParams.username),
                method: 'GET'
            }).success(function(data){
                console.log($scope);
                $scope.sources = data;
                console.log($scope.sources);
            }).error(function(){
                console.log('error');
                flash.push({
                    type: 'error',
                    message: 'An error as ocurred when fetching sources! Please try again later'
                });
            });
        };

        $scope.openSourceModal = function(){
            $scope.newSource = "";
            $("#new-source-modal").openModal();
        };

        $scope.removeSource = function(source) {
            $http({
                url: config.routes.api.source($routeParams.username) + '/delete/' + source._id.$id,
                method: 'POST'
            }).success(function(){
                flash.push({
                    type: 'success',
                    message: 'Source Delete'
                });

                that.fetchSources();
            }).error(function(){
                flash.push({
                    type: 'error',
                    message: 'An error as occurred'
                });
            });
        };

        $scope.createSource = function(){
            $http({
                url: config.routes.api.source($routeParams.username),
                method: 'POST',
                data: {source:$scope.newSource}
            }).success(function(){
                flash.push({
                    type: 'success',
                    message: 'Source Created'
                });
                that.fetchSources();
            }).error(function(){
                flash.push({
                    type: 'error',
                    message: 'An error as occurred while '
                });
            });

            $("#new-source-modal").closeModal();
        };

        this.fetchSources();
    }
]);