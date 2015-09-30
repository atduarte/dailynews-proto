var newsApp = angular.module('newsAppServices',['newsAppConfig']);

newsApp.factory('flash',['config','$timeout','$rootScope', function(config,$timeout,$rootScope) {
    var messages = [];
    return {
        push : function(message){
            messages.push(message);
            $rootScope.$emit(config.events.flash.push,message);
        }
    }
}]);