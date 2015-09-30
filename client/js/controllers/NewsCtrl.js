'use strict';


var newsAppController = angular.module('angularControllers');
newsAppController.controller('NewsCtrl', ['$scope','$routeParams','$http','config','flash',
function($scope,$routeParams,$http,config,flash){

    $scope.user = {};
    $scope.user.username = $routeParams.username;

    $http({
        method: "GET",
        url: config.routes.api.news($routeParams.username)
    }).success(function(data){
        console.log(data);
        $scope.newsEntrys = data;
        $scope.currentNews= data[0];
        $scope.selectedIndex = 0;

    }).error(function(){
        console.log("error");
    });

    $scope.collapsible = function(){
        console.log("Here11");
        $('.collapsible').collapsible({
            accordion : false // A setting that changes the collapsible behavior to expandable instead of the default accordion style
        });
    };

    $scope.setCurrent = function(current,index){
        $scope.currentNews = current;
        console.log("Index"+index);
        $scope.selectedIndex = index;
    }

}
]);