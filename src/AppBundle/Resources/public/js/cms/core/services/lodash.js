(function (angular) {
    "use strict";

    angular
        .module('cms.core')
        .factory('_', ['$window', function ($window) {
            return $window._;
        }]);

})(angular);