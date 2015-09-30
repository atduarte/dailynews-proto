
var newsAppController = angular.module('angularControllers');

newsAppController.controller('HeaderCtrl', ['$scope','$rootScope','$routeParams','config',
    function($scope,$rootScope,$routeParams,config) {
        $scope.messages = [];

        $scope.$on('$routeChangeSuccess', function() {
            if($routeParams.username){
                $scope.user = {};
                $scope.user.username = $routeParams.username;
            }
        });

        $rootScope.$on(config.events.flash.push,function(event,args){
            Materialize.toast(args.message, config.timeout.flash);
        });

    }
]);