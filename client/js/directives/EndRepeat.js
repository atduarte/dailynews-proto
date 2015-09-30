var newsApp = angular.module('newsAppDirectives');

newsApp.directive('endRepeat', function() {
    console.log("here0");
    return {
        restrict: 'A',
        link:function($scope, $element,$attrs) {
            console.log("melhoragora");
            if ($scope.$last) {
                $scope.$eval($attrs['endRepeat']);
            }
        }
    }
});
