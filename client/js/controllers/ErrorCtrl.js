'use strict';

var newsAppController = angular.module('angularControllers');

newsAppController.controller('ErrorCtrl', ['$scope','$location',
    function($scope,$location){
        $scope.login = function(){
            var request = $.post("http://api.dailynews.io/user/"+$scope.user.username);

            request.done(function(xhr, result) {
                console.log("login successful");
                $location.path('/user/'+$scope.user.username);
                $scope.$apply()
            });

            request.error(function(jqXHR, textStatus, errorThrown) {
                if(jqXHR.status === 500 && jqXHR.statusText === "OK"){
                    $location.path('/user/'+$scope.user.username);
                    $scope.$apply();
                }else{
                    console.log("Some other error: "+JSON.stringify(jqXHR));
                }
            });

        };
    }
]);